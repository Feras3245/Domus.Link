<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('immobilie')->constrained('immobilien')->onDelete('cascade');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('rent');
            $table->float('yield', 2)
                ->storedAs('IF(price > 0, ROUND((rent * 12.0 / price) * 100, 2), 0)');
            $table->float('area');
            $table->float('rooms');
            $table->timestamps();
        });

        $immobilien = DB::table('immobilien')->select('id', 'price', 'rent', 'area', 'rooms')->get();

        foreach ($immobilien as $immobilie) {
            DB::table('units')->insert([
                'immobilie' => $immobilie->id,
                'price' => $immobilie->price,
                'rent' => $immobilie->rent,
                'area' => $immobilie->area,
                'rooms' => $immobilie->rooms
            ]);
        }

        Schema::table('immobilien', function (Blueprint $table) {
            $table->dropColumn(['rent', 'price', 'area', 'rooms', 'yield']);
        });
    }

    public function down(): void
    {
        Schema::table('immobilien', function (Blueprint $table) {
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedBigInteger('rent')->nullable();
            $table->float('area')->nullable();
            $table->unsignedInteger('rooms')->nullable();
            $table->float('yield', 2)->nullable()
                ->storedAs('IF(price > 0, ROUND((rent * 12.0 / price) * 100, 2), 0)');
        });

        $units = DB::table('units')->select('immobilie', 'price', 'rent', 'area', 'rooms')->get();

        foreach ($units as $unit) {
            DB::table('immobilien')
                ->where('id', $unit->immobilie)
                ->update([
                    'price' => $unit->price,
                    'rent' => $unit->rent,
                    'area' => $unit->area,
                    'rooms' => $unit->rooms,
                ]);
        }

        Schema::dropIfExists('units');
    }
};
