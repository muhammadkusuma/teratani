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
        Schema::create('tenants', function (Blueprint $table) {
            $table->increments('id_tenant');
            $table->string('nama_bisnis', 100);
            $table->string('kode_unik_tenant', 20)->unique()->nullable();
            $table->text('alamat_kantor_pusat')->nullable();
            $table->string('owner_contact', 20)->nullable();
            $table->enum('paket_layanan', ['Trial', 'Basic', 'Pro', 'Enterprise'])->default('Trial');
            $table->integer('max_toko')->default(1);
            $table->enum('status_langganan', ['Aktif', 'Suspend', 'Expired'])->default('Aktif');
            $table->date('tgl_expired')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
