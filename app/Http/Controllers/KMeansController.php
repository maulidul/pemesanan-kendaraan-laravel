<?php

namespace App\Http\Controllers;

use App\Models\KmeansResult;
use App\Models\Vehicle;
use App\Services\KMeansService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KMeansController extends Controller
{
    /**
     * Halaman utama K-Means: input parameter dan tampilkan form.
     */
    public function index(): View
    {
        $service = new KMeansService();
        $features = $service->extractFeatures();

        // Ambil hasil analisis terakhir dari database
        $latestResults = KmeansResult::with('vehicle')
            ->orderByDesc('iteration')
            ->get()
            ->groupBy('cluster_name');

        // Statistik cluster (untuk badge ringkasan)
        $clusterStats = [];
        foreach ($latestResults as $name => $items) {
            $clusterStats[] = [
                'name'  => $name,
                'count' => $items->count(),
                'avg_frequency' => round($items->avg('frequency'), 2),
            ];
        }

        return view('kmeans.index', [
            'features'      => $features,
            'clusterStats'  => $clusterStats,
            'latestResults' => $latestResults,
            'totalVehicles' => count($features),
        ]);
    }

    /**
     * Jalankan analisis K-Means dan tampilkan hasilnya.
     */
    public function analyze(Request $request): View
    {
        $request->validate([
            'k'              => 'required|integer|min:2|max:5',
            'max_iterations' => 'required|integer|min:1|max:500',
            'tolerance'      => 'required|numeric|min:0.0001|max:1',
        ]);

        $k             = (int) $request->k;
        $maxIterations = (int) $request->max_iterations;
        $tolerance     = (float) $request->tolerance;

        $service = new KMeansService($k, $maxIterations, $tolerance);

        // 1) Ekstraksi fitur
        $features = $service->extractFeatures();

        // 2) Jalankan algoritma K-Means
        $result = $service->run($features);

        // 3) Simpan hasil ke DB jika konvergen
        if ($result['converged']) {
            $service->saveResults($result);
        }

        // 4) Siapkan data untuk ditampilkan di view
        $rows = [];
        foreach ($features as $idx => $feature) {
            $rows[] = [
                'vehicle_id'  => $feature['vehicle_id'],
                'nama'        => $feature['nama'],
                'plat'        => $feature['plat'],
                'frequency'   => $feature['frequency'],
                'duration'    => $feature['duration'],
                'intensity'   => $feature['intensity'],
                'cluster'     => $result['cluster_assignments'][$idx],
                'cluster_name'=> $result['cluster_names'][$result['cluster_assignments'][$idx]],
                'distance'    => $result['distances'][$idx],
            ];
        }

        // Ringkasan per cluster
        $clusterSummary = [];
        foreach ($result['cluster_names'] as $cIdx => $name) {
            $items = array_filter($rows, fn($r) => $r['cluster'] === $cIdx);
            $clusterSummary[$cIdx] = [
                'name'           => $name,
                'count'          => count($items),
                'avg_frequency'  => count($items) ? round(array_sum(array_column($items, 'frequency')) / count($items), 2) : 0,
                'avg_duration'   => count($items) ? round(array_sum(array_column($items, 'duration')) / count($items), 2) : 0,
                'avg_intensity'  => count($items) ? round(array_sum(array_column($items, 'intensity')) / count($items), 2) : 0,
            ];
        }

        return view('kmeans.result', [
            'rows'           => $rows,
            'result'         => $result,
            'clusterSummary' => $clusterSummary,
            'k'              => $k,
            'maxIterations'  => $maxIterations,
            'tolerance'      => $tolerance,
        ]);
    }

    /**
     * Reset / hapus hasil analisis K-Means.
     */
    public function reset()
    {
        KmeansResult::truncate();
        return redirect()->route('kmeans.index')
            ->with('success', 'Hasil analisis K-Means berhasil direset.');
    }
}