<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    public function immobilien(Request $request) 
    {
        $stats = Unit::query()->selectRaw('
            MAX(rent) as max_rent, MIN(rent) as min_rent,
            MAX(area) as max_area, MIN(area) as min_area,
            MAX(yield) as max_yield, MIN(yield) as min_yield,
            MAX(price) as max_price, MIN(price) as min_price
        ')->first();

        $rawLocations = Location::all();
        $locations = [];
        foreach($rawLocations as $rawLocation) {
            array_push($locations, $rawLocation['city'], $rawLocation['state']);
        }
        $locations = array_values(array_unique($locations));

        return response()->json([
            'locations' => $locations,
            'rent'  => ['min' => $stats->min_rent,  'max' => $stats->max_rent],
            'yield' => ['min' => $stats->min_yield, 'max' => $stats->max_yield],
            'area'  => ['min' => $stats->min_area,  'max' => $stats->max_area],
            'price' => ['min' => $stats->min_price, 'max' => $stats->max_price]
        ]);
    }

    public function unitTypes(Request $request) 
    {
        $unitTypes = UnitType::pluck('type');
        $meta = [];
        $meta['unit_types'] = $unitTypes;

        return response($meta, 200);
    }
}
