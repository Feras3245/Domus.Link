<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImmobilieResource;
use App\Models\Immobilie;
use App\Models\Location;
use App\Models\Translation;
use App\Models\Unit;
use App\Models\UnitType;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ImmobilienController extends Controller
{
    public function index(Request $request)
    {
        $fields = $request->validate([
            'pmax' => 'required_with:pmin|numeric|gte:pmin',
            'pmin' => 'required_with:pmax|numeric|lte:pmax',
            'rmax' => 'required_with:rmin|numeric|gte:rmin',
            'rmin' => 'required_with:rmax|numeric|lte:rmax',
            'ymax' => 'required_with:ymin|numeric|gte:ymin',
            'ymin' => 'required_with:ymax|numeric|lte:ymax',
            'amax' => 'required_with:amin|numeric|gte:amin',
            'amin' => 'required_with:amax|numeric|lte:amax',
            'usage' => 'sometimes|in:WOHNEN,MICRO,PFLEGE',
            'type' => 'sometimes|in:NEUBAU,BESTAND',
            'locations' => 'sometimes|string',
            'sort' => 'required_with:order|in:PRICE,RENT,YIELD,AREA',
            'order' => 'required_with:sort|in:ASC,DSC',
            'per_page' => 'sometimes|numeric',
        ]);

        $pmax = $fields['pmax'] ?? null;
        $pmin = $fields['pmin'] ?? null;
        $rmax = $fields['rmax'] ?? null;
        $rmin = $fields['rmin'] ?? null;
        $ymax = $fields['ymax'] ?? null;
        $ymin = $fields['ymin'] ?? null;
        $amax = $fields['amax'] ?? null;
        $amin = $fields['amin'] ?? null;
        $usage = $fields['usage'] ?? null;
        $type = $fields['type'] ?? null;
        $sort = $fields['sort'] ?? null;
        $order = $fields['order'] ?? null;
        $per_page = $fields['per_page'] ?? 6;
        $locations = array_key_exists('locations', $fields) ? array_map('trim', explode(",", strtolower($fields['locations']))) : null;

        $query = Immobilie::query();

        if ($request->user()->role !== 'ADMIN') {
            $query->where('hidden', false);
        }

        if (!is_null($usage)) $query->where('usage', $usage);
        if (!is_null($type)) $query->where('type', $type);

        $query->whereHas('units', function ($unitQuery) use ($pmin, $pmax, $rmin, $rmax, $ymin, $ymax, $amin, $amax) {
            if (!is_null($pmin)) $unitQuery->where('price', '>=', $pmin);
            if (!is_null($pmax)) $unitQuery->where('price', '<=', $pmax);
            if (!is_null($rmin)) $unitQuery->where('rent', '>=', $rmin);
            if (!is_null($rmax)) $unitQuery->where('rent', '<=', $rmax);
            if (!is_null($ymin)) $unitQuery->where('yield', '>=', $ymin);
            if (!is_null($ymax)) $unitQuery->where('yield', '<=', $ymax);
            if (!is_null($amin)) $unitQuery->where('area', '>=', $amin);
            if (!is_null($amax)) $unitQuery->where('area', '<=', $amax);
        });

        if ($locations) {
            $locations = DB::table('locations')->join('states', 'locations.state_id', '=', 'states.id')
            ->whereIn('city', $locations)
            ->orWhereIn('state', $locations)->get(['locations.id'])->toArray();
            $locationIds = [];
            foreach ($locations as $location) {
                array_push($locationIds, $location->id);
            }
            $query->whereIn('location_id', $locationIds);
        }
        
        if ($sort && $order) {
            $sort = strtolower($sort);
            $order = ($order === 'DSC') ? 'desc' : 'asc';
            $query->withMin('units', $sort)
                    ->orderBy("units_min_{$sort}", $order);
        }
        return $query->paginate($per_page)->withPath('/api/immobilien')->withQueryString()->toResourceCollection();
    }

    public function retrieve($id)
    {
        $immobilie = Immobilie::find($id);
        if (!$immobilie) {
            return response()->json(['error' => 'Requested resource is not found'], 404);
        }
        return new ImmobilieResource($immobilie);
    }

    public function create(Request $request)
    {
        $fields = $request->validate([
            'title.de'                => 'required|string|max:100',
            'title.en'                => 'required|string|max:100',
            'address'                 => 'required|string|max:100',
            'zip'                     => 'required|string|max:5',
            'city'                    => 'required|string|max:80',
            'state'                   => 'required|exists:states,state',
            'year'                    => 'required|integer|min:1000|max:'.now()->year,
            'type'                    => ['required', Rule::in(Immobilie::TYPE_OPTIONS)],
            'usage'                   => ['required', Rule::in(Immobilie::USAGE_OPTIONS)],
            'short_description.de'    => 'required|string|max:150',
            'short_description.en'    => 'required|string|max:150',
            'long_description.de'     => 'required|string|max:1200',
            'long_description.en'     => 'required|string|max:1200',
            'hidden'                  => 'sometimes|boolean',
            'units'                   => 'required|array|min:1',
            'units.*.price'           => 'required|numeric|min:0',
            'units.*.rent'            => 'required|numeric|min:0',
            'units.*.rooms'           => 'required|numeric|min:0',
            'units.*.area'            => 'required|numeric|min:0',
            'units.*.type'            => 'required|string|max:50'
        ]);

        $title = Translation::create($fields['title']);
        $short = Translation::create($fields['short_description']);
        $long = Translation::create($fields['long_description']);

        $stateId = DB::table('states')->where(['state' => $fields['state']])->first()->id;
        $location = Location::firstorCreate(['city'=>$fields['city'], 'state_id' => $stateId]);
        $hidden = $fields['hidden']??false;

        $immobilie = Immobilie::create([
            'title_id'             => $title->id,
            'short_description_id' => $short->id,
            'long_description_id'  => $long->id,
            'address'           => $fields['address'],
            'zip'               => $fields['zip'],
            'location_id'          => $location->id,
            'year'              => $fields['year'],
            'type'              => $fields['type'],
            'usage'             => $fields['usage'],
            'hidden'            => $hidden
        ]);

        foreach ($fields['units'] as $unit) {
            $type = UnitType::firstOrCreate(['type' => $unit['type']]);
            $immobilie->units()->create([
                'price' => $unit['price'],
                'rent'  => $unit['rent'],
                'rooms' => $unit['rooms'],
                'area'  => $unit['area'],
                'type_id'  => $type->id
            ]);
        }
        
        return new ImmobilieResource($immobilie);
    }

    public function toggleHidden($id) {
        $immobilie = Immobilie::find($id);
        if (!$immobilie) {
            return response()->json(['error' => 'Requested resource is not found'], 404);
        }

        $immobilie->hidden = !$immobilie->hidden;
        $immobilie->save();

        $message = $immobilie->hidden
            ? "Immobilie with ID {$id} is now hidden"
            : "Immobilie with ID {$id} is now visible";

        return response()->json(['message' => $message], 200);
    }

    public function delete($id) {
        $immobilie = Immobilie::find($id);
        if (!$immobilie) {
            return response()->json(['error' => 'Requested resource is not found'], 404);
        }
        $immobilie->delete();
        return response()->json(['message' => "Immobilie with ID {$id} have been successfully deleted."], 200);
    }


    public function update(Request $request, $id)
    {
        $immobilie = Immobilie::find($id);
        if (!$immobilie) {
            return response()->json(['error' => 'Requested resource is not found'], 404);
        }
        $fields = $request->validate([
            'title.de'             => 'sometimes|string|max:100',
            'title.en'             => 'sometimes|string|max:100',
            'address'              => 'sometimes|string|max:100',
            'zip'                  => 'sometimes|string|size:5',
            'city'                 => 'sometimes|string|max:80',
            'state'                => 'sometimes|exists:states,state',
            'price'                => 'sometimes|numeric|min:1',
            'rent'                 => 'sometimes|numeric|min:1',
            'rooms'                => 'sometimes|numeric|min:1',
            'area'                 => 'sometimes|numeric|min:1',
            'year'                 => 'sometimes|integer|min:1000|max:' . now()->year,
            'type'                 => ['sometimes', Rule::in(Immobilie::TYPE_OPTIONS)],
            'usage'                => ['sometimes', Rule::in(Immobilie::USAGE_OPTIONS)],
            'short_description.de' => 'sometimes|string|max:150',
            'short_description.en' => 'sometimes|string|max:150',
            'long_description.de'  => 'sometimes|string|max:1200',
            'long_description.en'  => 'sometimes|string|max:1200',
            'hidden'               => 'sometimes|boolean'
        ]);

        try {
            DB::beginTransaction();

            if (isset($fields['title'])) {
                $immobilie->title()->update($fields['title']);
            }

            if (isset($fields['short_description'])) {
                $immobilie->shortDescription()->update($fields['short_description']);
            }

            if (isset($fields['long_description'])) {
                $immobilie->longDescription()->update($fields['long_description']);
            }

            if (isset($fields['city']) || isset($fields['state'])) {
                $city = isset($fields['city']) ? trim($fields['city']) : $immobilie->location()->value('city');
                $stateId = isset($fields['state']) ? DB::table('states')->where(['state' => $fields['state']])->first()->id : $immobilie->location()->value('state_id');
                $location = Location::firstorCreate(['city' => $city, 'state_id' => $stateId]);
                $immobilie->location()->associate($location);
            }

            $immobilieData = Arr::except($fields, ['title', 'short_description', 'long_description', 'city', 'state']);
            
            if (!empty($immobilieData)) {
                $immobilie->update($immobilieData);
            }
            DB::commit();
            $immobilie->save();
            return new ImmobilieResource($immobilie->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}


