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
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->uuid('id_detail')->primary();
            $table->foreignUuid('id_transaksi')->constrained('transaksis', 'id_transaksi')->cascadeOnDelete();
            $table->foreignUuid('id_barang')->nullable()->constrained('barangs', 'id_barang')->nullOnDelete();
            $table->foreignUuid('id_jasa')->nullable()->constrained('jasas', 'id_jasa')->nullOnDelete();
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
