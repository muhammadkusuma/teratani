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
        Schema::create('user_tenant_mapping', function (Blueprint $table) {
            $table->increments('id_mapping');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_tenant');
            $table->enum('role_in_tenant', ['OWNER', 'MANAGER', 'ADMIN', 'KASIR']);
            $table->boolean('is_primary')->default(false);

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_tenant')->references('id_tenant')->on('tenants')->onDelete('cascade');
            $table->unique(['id_user', 'id_tenant'], 'unique_access');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tenant_mapping');
    }
};
