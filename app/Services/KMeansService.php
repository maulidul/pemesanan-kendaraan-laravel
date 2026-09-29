<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\KmeansResult;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * KMeansService
 *
 * Implementasi native PHP untuk Algoritma K-Means Clustering
 * yang diterapkan pada analisis pola pemakaian kendaraan dinas.
 *
 * Atribut yang dianalisis:
 *   - frequency : jumlah pemesanan per kendaraan
 *   - duration  : rata-rata durasi pemakaian (hari)
 *   - intensity : intensitas pemakaian (pemesanan per bulan aktif)
 *
 * Perhitungan jarak menggunakan Euclidean Distance:
 *   d(p, q) = sqrt((p1-q1)² + (p2-q2)² + (p3-q3)²)
 */
class KMeansService
{
    /** Jumlah cluster (K) */
    private int $k;

    /** Batas maksimum iterasi */
    private int $maxIterations;

    /** Threshold perubahan centroid untuk dianggap konvergen */
    private float $tolerance;

    /**
     * @param int   $k              Jumlah cluster
     * @param int   $maxIterations  Iterasi maksimum
     * @param float $tolerance      Threshold konvergensi
     */
    public function __construct(int $k = 3, int $maxIterations = 100, float $tolerance = 0.0001)
    {
        $this->k             = $k;
        $this->maxIterations = $maxIterations;
        $this->tolerance     = $tolerance;
    }

    /**
     * Ekstraksi fitur dari database untuk setiap kendaraan.
     *
     * @return array<int, array{vehicle_id:int, nama:string, plat:string, frequency:float, duration:float, intensity:float}>
     */
    public function extractFeatures(): array
    {
        // Menghitung jumlah pemesanan, durasi rata-rata, dan intensitas per kendaraan.
        $rows = DB::table('bookings')
            ->select(
                'vehicle_id',
                DB::raw('COUNT(*) as frequency'),
                DB::raw('AVG(DATEDIFF(tanggal_selesai, tanggal_mulai) + 1) as duration'),
                DB::raw('MIN(tanggal_pemesanan) as first_booking'),
                DB::raw('MAX(tanggal_pemesanan) as last_booking')
            )
            ->whereIn('status_persetujuan', ['disetujui', 'selesai'])
            ->groupBy('vehicle_id')
            ->get();

        $features = [];
        foreach ($rows as $row) {
            $spanMonths = max(1, $this->monthsBetween($row->first_booking, $row->last_booking));
            $intensity  = $row->frequency / $spanMonths;

            $vehicle = Vehicle::find($row->vehicle_id);

            $features[] = [
                'vehicle_id' => (int) $row->vehicle_id,
                'nama'       => $vehicle?->nama_kendaraan ?? 'Tidak Diketahui',
                'plat'       => $vehicle?->plat_nomor ?? '-',
                'frequency'  => (float) $row->frequency,
                'duration'   => (float) round($row->duration, 2),
                'intensity'  => (float) round($intensity, 2),
            ];
        }

        // Kendaraan yang belum pernah dibooking (agar tetap muncul di analisis)
        $existingIds = collect($features)->pluck('vehicle_id')->toArray();
        $vehiclesWithoutBooking = Vehicle::whereNotIn('id', $existingIds)->get();
        foreach ($vehiclesWithoutBooking as $vehicle) {
            $features[] = [
                'vehicle_id' => $vehicle->id,
                'nama'       => $vehicle->nama_kendaraan,
                'plat'       => $vehicle->plat_nomor,
                'frequency'  => 0,
                'duration'   => 0,
                'intensity'  => 0,
            ];
        }

        return $features;
    }

    /**
     * Hitung jumlah bulan antara dua tanggal.
     */
    private function monthsBetween(string $start, string $end): int
    {
        $startDate = new \DateTime($start);
        $endDate   = new \DateTime($end);
        $diff      = $startDate->diff($endDate);
        return ($diff->y * 12) + $diff->m + 1;
    }

    /**
     * Normalisasi fitur agar skala tiap atribut setara (min-max normalization).
     * Penting karena K-Means sensitif terhadap perbedaan skala.
     *
     * @param array<int, array<string, mixed>> $data
     * @return array{normalized: array<int, array<float>>, mins: array<float>, maxs: array<float>}
     */
    public function normalize(array $data): array
    {
        if (empty($data)) {
            return ['normalized' => [], 'mins' => [0, 0, 0], 'maxs' => [1, 1, 1]];
        }

        $attributeKeys = ['frequency', 'duration', 'intensity'];
        $mins = [];
        $maxs = [];

        foreach ($attributeKeys as $key) {
            $values = array_column($data, $key);
            $mins[$key] = min($values);
            $maxs[$key] = max($values);
            // Hindari pembagian dengan 0
            if ($mins[$key] === $maxs[$key]) {
                $maxs[$key] = $mins[$key] + 1;
            }
        }

        $normalized = [];
        foreach ($data as $point) {
            $row = [];
            foreach ($attributeKeys as $key) {
                $row[] = ($point[$key] - $mins[$key]) / ($maxs[$key] - $mins[$key]);
            }
            $normalized[] = $row;
        }

        return [
            'normalized' => $normalized,
            'mins'       => $mins,
            'maxs'       => $maxs,
        ];
    }

    /**
     * Hitung jarak Euclidean antara dua titik (array).
     *
     * Rumus: d(p, q) = sqrt(Σ (pi - qi)²)
     *
     * @param array<int|float> $p
     * @param array<int|float> $q
     */
    public function euclideanDistance(array $p, array $q): float
    {
        $sum = 0.0;
        $count = min(count($p), count($q));
        for ($i = 0; $i < $count; $i++) {
            $sum += pow($p[$i] - $q[$i], 2);
        }
        return sqrt($sum);
    }

    /**
     * Inisialisasi centroid awal dengan metode K-Means++ (deterministik).
     *
     * @param array<int, array<float>> $points
     * @return array<int, array<float>>
     */
    public function initializeCentroids(array $points): array
    {
        $centroids = [];
        // Centroid pertama: ambil titik dengan frequency tertinggi (deterministik)
        usort($points, fn($a, $b) => $b[0] <=> $a[0]);
        $centroids[] = $points[0];

        // Centroid selanjutnya: pilih titik yang jaraknya paling jauh dari centroid existing
        while (count($centroids) < $this->k) {
            $maxDist = -1;
            $farthest = $points[0];

            foreach ($points as $point) {
                // Skip jika titik ini sudah jadi centroid
                $isCentroid = false;
                foreach ($centroids as $c) {
                    if ($c === $point) {
                        $isCentroid = true;
                        break;
                    }
                }
                if ($isCentroid) continue;

                // Jarak minimum ke centroid yang sudah ada
                $minDistToCentroid = PHP_FLOAT_MAX;
                foreach ($centroids as $c) {
                    $d = $this->euclideanDistance($point, $c);
                    if ($d < $minDistToCentroid) {
                        $minDistToCentroid = $d;
                    }
                }

                if ($minDistToCentroid > $maxDist) {
                    $maxDist   = $minDistToCentroid;
                    $farthest  = $point;
                }
            }

            $centroids[] = $farthest;
        }

        return $centroids;
    }

    /**
     * Jalankan algoritma K-Means lengkap dan kembalikan hasil + history iterasi.
     *
     * @param array<int, array<string, mixed>> $rawData Data mentah dari extractFeatures()
     * @return array{
     *     converged: bool,
     *     iterations: int,
     *     centroids: array<int, array<float>>,
     *     cluster_assignments: array<int, int>,
     *     distances: array<int, float>,
     *     cluster_names: array<int, string>,
     *     history: array<int, array{centroids: array<int, array<float>>, assignments: array<int, int>}>,
     *     normalized: array<int, array<float>>,
     *     features: array<int, array<string, mixed>>
     * }
     */
    public function run(array $rawData): array
    {
        if (count($rawData) < $this->k) {
            return [
                'converged'           => false,
                'iterations'          => 0,
                'centroids'           => [],
                'cluster_assignments' => [],
                'distances'           => [],
                'cluster_names'       => $this->getClusterNames(),
                'history'             => [],
                'normalized'          => [],
                'features'            => $rawData,
            ];
        }

        // Normalisasi data
        $normResult   = $this->normalize($rawData);
        $points       = $normResult['normalized'];
        $mins         = $normResult['mins'];
        $maxs         = $normResult['maxs'];

        // Inisialisasi centroid awal
        $centroids = $this->initializeCentroids($points);

        $assignments     = array_fill(0, count($points), -1);
        $distances       = array_fill(0, count($points), 0.0);
        $history         = [];
        $converged       = false;
        $iterationCount  = 0;

        // Iterasi K-Means
        for ($iter = 1; $iter <= $this->maxIterations; $iter++) {
            $iterationCount = $iter;
            $newAssignments = [];
            $newDistances   = [];

            // STEP 1: Assignment - tentukan cluster untuk setiap titik
            foreach ($points as $idx => $point) {
                $minDist  = PHP_FLOAT_MAX;
                $bestCluster = 0;
                foreach ($centroids as $cIdx => $centroid) {
                    $d = $this->euclideanDistance($point, $centroid);
                    if ($d < $minDist) {
                        $minDist     = $d;
                        $bestCluster = $cIdx;
                    }
                }
                $newAssignments[$idx] = $bestCluster;
                $newDistances[$idx]   = $minDist;
            }

            // STEP 2: Update centroid - hitung rata-rata titik di tiap cluster
            $newCentroids = [];
            $sumPerCluster = array_fill(0, $this->k, array_fill(0, count($points[0]), 0.0));
            $countPerCluster = array_fill(0, $this->k, 0);

            foreach ($points as $idx => $point) {
                $cluster = $newAssignments[$idx];
                $countPerCluster[$cluster]++;
                foreach ($point as $dim => $val) {
                    $sumPerCluster[$cluster][$dim] += $val;
                }
            }

            foreach ($centroids as $cIdx => $_) {
                if ($countPerCluster[$cIdx] > 0) {
                    $newCentroids[$cIdx] = array_map(
                        fn($s) => $s / $countPerCluster[$cIdx],
                        $sumPerCluster[$cIdx]
                    );
                } else {
                    // Cluster kosong, pertahankan centroid lama
                    $newCentroids[$cIdx] = $centroids[$cIdx];
                }
            }

            // Cek konvergensi: apakah centroid tidak banyak berubah?
            $maxShift = 0.0;
            foreach ($newCentroids as $cIdx => $newC) {
                $shift = $this->euclideanDistance($centroids[$cIdx], $newC);
                if ($shift > $maxShift) {
                    $maxShift = $shift;
                }
            }

            // Simpan history
            $history[] = [
                'iteration'   => $iter,
                'centroids'   => $centroids,
                'assignments' => $newAssignments,
                'max_shift'   => round($maxShift, 6),
            ];

            // Update
            $centroids   = $newCentroids;
            $assignments = $newAssignments;
            $distances   = $newDistances;

            if ($maxShift < $this->tolerance) {
                $converged = true;
                break;
            }
        }

        // Tentukan label cluster berdasarkan rata-rata frequency (cluster 0 = tertinggi)
        $clusterSums = array_fill(0, $this->k, 0);
        $clusterCounts = array_fill(0, $this->k, 0);
        foreach ($assignments as $idx => $cluster) {
            $clusterSums[$cluster]   += $rawData[$idx]['frequency'];
            $clusterCounts[$cluster]++;
        }
        $clusterAvg = [];
        foreach ($clusterSums as $cIdx => $sum) {
            $clusterAvg[$cIdx] = $clusterCounts[$cIdx] > 0 ? $sum / $clusterCounts[$cIdx] : 0;
        }
        // Urutkan cluster dari frequency tertinggi ke terendah
        arsort($clusterAvg);
        $labelMapping = array_keys($clusterAvg);
        // Map: cluster dengan frequency tertinggi => 'Frekuensi Tinggi' (label 0)
        $clusterNames = $this->getClusterNames();
        $finalAssignments = [];
        $finalDistances   = [];
        foreach ($assignments as $idx => $origCluster) {
            $finalAssignments[$idx] = (int) array_search($origCluster, $labelMapping);
            $finalDistances[$idx]   = $distances[$idx];
        }

        return [
            'converged'           => $converged,
            'iterations'          => $iterationCount,
            'centroids'           => $centroids,
            'cluster_assignments' => $finalAssignments,
            'distances'           => $finalDistances,
            'cluster_names'       => $clusterNames,
            'history'             => $history,
            'normalized'          => $points,
            'features'            => $rawData,
        ];
    }

    /**
     * Daftar nama cluster berdasarkan urutan frequency (0 = tertinggi).
     * Otomatis menyesuaikan dengan jumlah K (2-5).
     *
     * @return array<int, string>
     */
    public function getClusterNames(): array
    {
        // Template label untuk K yang umum
        $labelsFor3 = [
            0 => 'Frekuensi Tinggi',
            1 => 'Frekuensi Sedang',
            2 => 'Frekuensi Rendah',
        ];

        if ($this->k === 3) {
            return $labelsFor3;
        }

        // Untuk K != 3, gunakan label generic + suffix
        $prefix = match (true) {
            $this->k === 2 => 'Frekuensi',
            $this->k === 4 => 'Frekuensi',
            $this->k === 5 => 'Frekuensi',
            default        => 'Cluster',
        };

        $suffixes = [
            2 => ['Tinggi', 'Rendah'],
            4 => ['Sangat Tinggi', 'Tinggi', 'Sedang', 'Rendah'],
            5 => ['Sangat Tinggi', 'Tinggi', 'Sedang', 'Rendah', 'Sangat Rendah'],
        ];

        $suffixList = $suffixes[$this->k] ?? ['1', '2', '3', '4', '5'];
        $names = [];
        foreach ($suffixList as $idx => $suffix) {
            $names[$idx] = "{$prefix} {$suffix}";
        }
        return $names;
    }

    /**
     * Simpan hasil clustering ke tabel kmeans_results.
     *
     * @param array $result Output dari method run()
     */
    public function saveResults(array $result): void
    {
        if (!($result['converged'] ?? false)) {
            return;
        }

        // Kosongkan hasil lama
        KmeansResult::truncate();

        $iteration = $result['iterations'];
        $clusterNames = $result['cluster_names'];
        $assignments  = $result['cluster_assignments'];
        $distances    = $result['distances'];
        $features     = $result['features'];

        foreach ($features as $idx => $feature) {
            KmeansResult::create([
                'vehicle_id'   => $feature['vehicle_id'],
                'cluster_label'=> $assignments[$idx],
                'cluster_name' => $clusterNames[$assignments[$idx]],
                'frequency'    => $feature['frequency'],
                'duration'     => $feature['duration'],
                'intensity'    => $feature['intensity'],
                'distance'     => round($distances[$idx], 4),
                'iteration'    => $iteration,
            ]);
        }
    }
}