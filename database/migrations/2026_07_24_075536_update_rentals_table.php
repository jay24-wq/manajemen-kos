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
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'monthly_price']);

            $table->foreignId('branch_id')->constrained('branches');

            $table->enum('tipe_kontrak', ['bulanan', 'kontrak']);
            $table->date('tanggal_mulai');
            $table->unsignedInteger('durasi')->nullable();
            $table->decimal('harga_bulanan', 12, 2);
            $table->decimal('total_harga', 14, 2)->nullable();
            $table->text('alasan_batal')->nullable();
            $table->boolean('dp_hangus')->default(false);
            $table->text('catatan_dp_hangus')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->index(['status', 'tipe_kontrak']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn([
                'branch_id', 'tipe_kontrak', 'tanggal_mulai', 'durasi',
                'harga_bulanan', 'total_harga', 'alasan_batal',
                'dp_hangus', 'catatan_dp_hangus', 'tanggal_selesai'
            ]);

            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_price', 12, 2);
        });
    }
};
