<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('immobilien', function (Blueprint $table) {
            $table->renameColumn('title', 'title_id');
            $table->renameColumn('short_description', 'short_description_id');
            $table->renameColumn('long_description', 'long_description_id');
            $table->renameColumn('location', 'location_id');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->renameColumn('type', 'type_id');
            $table->renameColumn('immobilie', 'immobilie_id');
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
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
            $table->unique(['state']);
        });

        $states = [
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
        ];
        $stateIds = [];
        foreach ($states as $state) {
            $stateId = DB::table('states')->insertGetId(['state' => $state]);
            $stateIds[$state] = $stateId;
        }

        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->after('state')->constrained('states')->onDelete('cascade');
        });

        $locations = DB::table('locations')->select(['id', 'city', 'state'])->get();

        foreach ($locations as $location) {
            DB::table('locations')
            ->where('id', $location->id)
            ->update(['state_id' => $stateIds[$location->state]]);
        }

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->foreignId('state_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
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
            ])->nullable()->after('city');
        });

        $stateIdToName = DB::table('states')->pluck('state', 'id');

        $locations = DB::table('locations')->select(['id', 'state_id'])->get();

        foreach ($locations as $location) {
            DB::table('locations')
                ->where('id', $location->id)
                ->update(['state' => $stateIdToName[$location->state_id]]);
        }

        Schema::table('locations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
        });

        Schema::dropIfExists('states');

        Schema::table('units', function (Blueprint $table) {
            $table->renameColumn('type_id', 'type');
            $table->renameColumn('immobilie_id', 'immobilie');
        });

        Schema::table('immobilien', function (Blueprint $table) {
            $table->renameColumn('title_id', 'title');
            $table->renameColumn('short_description_id', 'short_description');
            $table->renameColumn('long_description_id', 'long_description');
            $table->renameColumn('location_id', 'location');
        });
    }

};
