<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
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
        });

        // the lcoation column should be made nullable initially so we can add the column to the existing table
        Schema::table('immobilien', function (Blueprint $table) {
            $table->foreignId('location')->nullable()->after('state')->constrained('locations')->onDelete('cascade');
        });

        $immobilien = DB::table('immobilien')->select('id', 'city', 'state')->get();

        foreach ($immobilien as $immobilie) {
            // fetch the location or create new one if none exist
            $location = DB::table('locations')
                ->where('city', $immobilie->city)
                ->where('state', $immobilie->state)
                ->first();

            if (!$location) {
                $locationId = DB::table('locations')->insertGetId([
                    'city' => $immobilie->city,
                    'state' => $immobilie->state
                ]);
                $location = (object)['id' => $locationId];
            }

            // update the 'immobilien' record with the foreign key of the new or existing location
            DB::table('immobilien')
            ->where('id', $immobilie->id)
            ->update(['location' => $location->id]);
        }

        Schema::table('immobilien', function (Blueprint $table) {
            $table->dropColumn(['city', 'state']);
            // change the 'location' column to be non-nullable after populating it
            $table->foreignId('location')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('immobilien', function (Blueprint $table) {
            $table->string('city', 80)->nullable()->after('location');
            $table->enum('state', [
                'Baden-Württemberg', 'Bayern', 'Berlin', 'Brandenburg', 'Bremen',
                'Hamburg', 'Hessen', 'Mecklenburg-Vorpommern', 'Niedersachsen',
                'Nordrhein-Westfalen', 'Rheinland-Pfalz', 'Saarland', 'Sachsen',
                'Sachsen-Anhalt', 'Schleswig-Holstein', 'Thüringen'
            ])->nullable()->after('city');
        });

        $immobilien = DB::table('immobilien')
        ->join('locations', 'immobilien.location', '=', 'locations.id')
        ->select('immobilien.id', 'locations.city', 'locations.state')
        ->get();

        foreach ($immobilien as $immobilie) {
            DB::table('immobilien')
                ->where('id', $immobilie->id)
                ->update([
                    'city' => $immobilie->city,
                    'state' => $immobilie->state
                ]);
        }

        Schema::table('immobilien', function (Blueprint $table) {
            $table->dropForeign(['location']);
            $table->dropColumn('location');
            $table->string('city', 80)->nullable(false)->change();
            $table->enum('state', [
                'Baden-Württemberg', 'Bayern', 'Berlin', 'Brandenburg', 'Bremen',
                'Hamburg', 'Hessen', 'Mecklenburg-Vorpommern', 'Niedersachsen',
                'Nordrhein-Westfalen', 'Rheinland-Pfalz', 'Saarland', 'Sachsen',
                'Sachsen-Anhalt', 'Schleswig-Holstein', 'Thüringen'
            ])->nullable(false)->change();
        });

        Schema::dropIfExists('locations');
    }
};
