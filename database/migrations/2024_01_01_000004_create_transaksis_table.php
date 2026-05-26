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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->uuid('id_transaksi')->primary();
            $table->foreignUuid('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->decimal('total_pembayaran', 10, 2)->default(0);
            $table->string('metode_bayar', 20)->default('cash'); // cash, midtrans
            $table->string('status_bayar', 20)->default('lunas'); // lunas, pending, gagal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
