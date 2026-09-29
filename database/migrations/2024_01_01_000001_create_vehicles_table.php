<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kendaraan');
            $table->string('plat_nomor')->unique();
            $table->enum('jenis_kendaraan', ['Sedan', 'SUV', 'Minibus', 'Pick Up', 'Truk', 'Bus', 'Motor Dinas']);
            $table->integer('kapasitas_penumpup')->default(4);
            $table->enum('status_ketersediaan', ['Tersedia', 'Tidak Tersedia', 'Dalam Perbaikan', 'Digunakan'])
                  ->default('Tersedia');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};