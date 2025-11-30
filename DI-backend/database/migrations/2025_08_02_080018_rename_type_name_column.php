<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_types', function (Blueprint $table) {
            $table->renameColumn('name', 'type');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->unique(['city', 'state_id']);
        });
    }

    public function down(): void
    {
        Schema::table('unit_types', function (Blueprint $table) {
            $table->renameColumn('type', 'name');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropUnique(['city', 'state_id']);
        });
    }
};
