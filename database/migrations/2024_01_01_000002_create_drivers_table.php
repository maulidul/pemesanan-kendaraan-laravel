<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_driver');
            $table->string('nomor_telepon', 20);
            $table->string('nomor_sim')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif', 'Cuti'])
                  ->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};