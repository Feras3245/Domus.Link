<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Storage::disk('local')->deleteDirectory("/images");
        Schema::create('images', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('owner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
