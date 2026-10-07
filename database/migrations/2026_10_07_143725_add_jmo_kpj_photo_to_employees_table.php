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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('no_jmo')->nullable()->after('email');
            $table->string('no_kpj')->nullable()->after('no_jmo');
            $table->string('photo_path')->nullable()->after('no_kpj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['no_jmo', 'no_kpj', 'photo_path']);
        });
    }
};
