<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kmeans_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->cascadeOnDelete();
            $table->integer('cluster_label'); // 0 = Tinggi, 1 = Sedang, 2 = Rendah
            $table->string('cluster_name'); // 'Frekuensi Tinggi', 'Frekuensi Sedang', 'Frekuensi Rendah'
            $table->decimal('frequency', 8, 2); // Jumlah pemesanan
            $table->decimal('duration', 8, 2);   // Rata-rata durasi (hari)
            $table->decimal('intensity', 8, 2);  // Intensitas pemakaian
            $table->decimal('distance', 10, 4);  // Jarak Euclidean ke centroid
            $table->integer('iteration');        // Iterasi ke berapa saat konvergen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kmeans_results');
    }
};