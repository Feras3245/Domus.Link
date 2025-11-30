<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('type')->after('immobilie')->nullable()->constrained('unit_types')->onDelete('cascade');
        });

        $unitTypeId = DB::table('unit_types')->insertGetId(['name' => 'wohnung']);

        DB::table('units')->update(['type' => $unitTypeId]);

        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('type')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['type']);
            $table->dropColumn('type');
        });

        Schema::dropIfExists('unit_types');
    }
};
