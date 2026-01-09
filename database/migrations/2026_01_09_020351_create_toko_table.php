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
        Schema::create('toko', function (Blueprint $table) {
            $table->increments('id_toko');
            $table->unsignedInteger('id_tenant');
            $table->string('kode_toko', 20)->nullable();
            $table->string('nama_toko', 100);
            $table->text('alamat')->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->boolean('is_pusat')->default(false);

            $table->foreign('id_tenant')->references('id_tenant')->on('tenants')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko');
    }
};
