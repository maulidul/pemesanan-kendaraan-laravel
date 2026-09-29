<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->cascadeOnDelete();
            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('drivers')
                  ->nullOnDelete();
            $table->date('tanggal_pemesanan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('tujuan');
            $table->text('keperluan');
            $table->integer('jumlah_penumpup')->default(1);
            $table->enum('status_persetujuan', ['pending', 'disetujui', 'ditolak', 'selesai'])
                  ->default('pending');
            $table->text('catatan_persetujuan')->nullable();
            $table->timestamp('tanggal_diseputujui')->nullable();
            $table->timestamps();

            // Index untuk performa query pada analisis K-Means
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');
            $table->index('status_persetujuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};