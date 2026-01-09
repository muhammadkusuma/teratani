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
        Schema::create('stok_toko', function (Blueprint $table) {
            $table->increments('id_stok');
            $table->unsignedInteger('id_toko');
            $table->unsignedInteger('id_produk');
            $table->integer('stok_fisik')->default(0);
            $table->integer('stok_minimal')->default(5);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->string('lokasi_rak', 50)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_toko')->references('id_toko')->on('toko')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->unique(['id_toko', 'id_produk'], 'unique_stok');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_toko');
    }
};
