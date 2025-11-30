<?php

namespace App\Http\Controllers;

use App\Models\Immobilie;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UnitController extends Controller
{
    public function create(Request $request) {
        $fields = $request->validate([
            'immobilie' => 'required|exists:immobilien,id',
            'price'           => 'required|numeric|min:0',
            'rent'            => 'required|numeric|min:0',
            'rooms'           => 'required|numeric|min:0',
            'area'            => 'required|numeric|min:0',
            'type'            => 'required|string|max:50'
        ]);

        $immobilie = Immobilie::find($fields['immobilie']);
        if (!$immobilie) {
            return response()->json(['error' => 'Immobilie ID is not found'], 404);
        }

        $typeId = UnitType::firstOrCreate(['type' => $fields['type']])->id;

        $fields['type_id'] = $typeId;
        unset($fields['type']);

        $unit = $immobilie->units()->create($fields);
        return response($unit, 200);
    }

    public function update(Request $request, $id) {
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json(['error' => "Unit with ID {$id} was not found"], 404);
        }
        
        $fields = $request->validate([
            'price'     => 'required_without_all:rent,rooms,area,type|numeric|min:0',
            'rent'      => 'required_without_all:price,rooms,area,type|numeric|min:0',
            'area'      => 'required_without_all:price,rooms,rent,type|numeric|min:0',
            'rooms'     => 'required_without_all:price,rent,area,type|numeric|min:0',
            'type'      => 'required_without_all:price,rent,area,rooms|string|max:50'
        ]);

        if (isset($fields['type'])) {
            $typeId = UnitType::firstOrCreate(['type' => $fields['type']])->id;
            $fields['type_id'] = $typeId;
            unset($fields['type']);
        }

        $attributes = Arr::except($fields, ['id']);
        $unit->update($attributes);
        return response($unit, 200);
    }

    public function delete($id) {   
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json(['error' => "Unit with ID {$id} was not found"], 404);
        }
        $unit->delete();
        return response(['message' => "Successfully deleted unit with ID {$id}."], 200);
    }

    public function params() {
        $params = [];
        $params['unit_types'] = UnitType::pluck('type');
        return response($params, 200);
    }
}
