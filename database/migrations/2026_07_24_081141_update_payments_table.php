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
            $table->dropColumn(['period_month', 'amount', 'payment_date', 'status', 'method', 'notes']);

            $table->string('periode', 7);
            $table->decimal('jumlah_dibayar', 12, 2);
            $table->date('tanggal_bayar');
            $table->enum('metode', ['transfer', 'cash']);
            $table->text('catatan')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');

            $table->index(['rental_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['rental_id', 'periode']);
            $table->dropForeign(['recorded_by']);
            $table->dropColumn(['periode', 'jumlah_dibayar', 'tanggal_bayar', 'metode', 'catatan', 'recorded_by']);

            $table->string('period_month');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->enum('status', ['lunas', 'kurang', 'dibatalkan'])->default('lunas');
            $table->enum('method', ['cash', 'bank'])->default('cash');
            $table->text('notes')->nullable();
        });
    }
};
