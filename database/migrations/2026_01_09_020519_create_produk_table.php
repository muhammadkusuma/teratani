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
        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id_produk');
            $table->unsignedInteger('id_tenant');
            $table->unsignedInteger('id_kategori')->nullable();
            $table->unsignedInteger('id_satuan')->nullable();
            $table->string('sku', 50)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->string('nama_produk', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_pokok_standar', 15, 2)->default(0);
            $table->integer('berat_gram')->default(0);
            $table->string('gambar_produk')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_tenant')->references('id_tenant')->on('tenants')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori');
            $table->foreign('id_satuan')->references('id_satuan')->on('satuan');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
