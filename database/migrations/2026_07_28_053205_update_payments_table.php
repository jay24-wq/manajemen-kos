<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // NULL untuk pembayaran tipe kontrak "bulanan" (pakai periode seperti biasa).
            // Diisi untuk tipe kontrak "kontrak" (durasi tetap): dp_awal, bayar_lunas, atau bayar_penuh.
            $table->enum('jenis_pembayaran', ['dp_awal', 'bayar_lunas', 'bayar_penuh'])
                ->nullable()
                ->after('metode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('jenis_pembayaran');
        });
    }
};
