<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmobilienTable extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->longText('de');
            $table->longText('en');
        });

        Schema::create('immobilien', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('title')->constrained('translations')->onDelete('cascade');
            $table->foreignId('short_description')->constrained('translations')->onDelete('cascade');
            $table->foreignId('long_description')->constrained('translations')->onDelete('cascade');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('rent');
            $table->float('area');
            $table->unsignedInteger('rooms');
            $table->unsignedInteger('year');
            $table->float('yield', 2)
                ->storedAs('IF(price > 0, ROUND((rent * 12.0 / price) * 100, 2), 0)');
            $table->string('address', 100);
            $table->string('zip', 5);
            $table->string('city', 80);
            $table->enum('state', [
                'Baden-Württemberg',
                'Bayern',
                'Berlin',
                'Brandenburg',
                'Bremen',
                'Hamburg',
                'Hessen',
                'Mecklenburg-Vorpommern',
                'Niedersachsen',
                'Nordrhein-Westfalen',
                'Rheinland-Pfalz',
                'Saarland',
                'Sachsen',
                'Sachsen-Anhalt',
                'Schleswig-Holstein',
                'Thüringen'
            ]);
            $table->enum('type', ['NEUBAU', 'BESTAND']);
            $table->enum('usage', ['WOHNEN', 'MICRO', 'PFLEGE']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immobilien');
        Schema::dropIfExists('translations');
    }
}
