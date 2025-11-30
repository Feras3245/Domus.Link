<?php

namespace Database\Seeders;


use App\Models\Image;
use App\Models\Immobilie;
use App\Models\Location;
use App\Models\Translation;
use App\Models\UnitType;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImmobilienSeeder extends Seeder
{
    public function run(): void
    {
        $immobilien = [];
        $rootpath = "immobilie-seeds";
        $immobilieFolders = Storage::disk('local')->directories($rootpath);
        if (count($immobilieFolders) === 0) {
            throw new \Exception("ERROR: ./storage/app/private/{$rootpath}/ directory is empty or does not exist.");
        }
        foreach ($immobilieFolders as $immobilieFolder) {
            $dataFile = Storage::disk('local')->get("{$immobilieFolder}/data.json");
            if (!$dataFile) {
                throw new \Exception("ERROR: {$immobilieFolder}/data.json file not found.");
            }
            $imageFiles = Storage::disk('local')->allFiles("{$immobilieFolder}/images");
            if (count($imageFiles) === 0) {
                throw new \Exception("ERROR: missing directory or no image files in {$immobilieFolder}/images/");
            }
            $images = [];
            foreach($imageFiles as $imageFile) {
                $fullpath = Storage::disk('local')->path($imageFile);
                $name = basename($imageFile);
                $image = new UploadedFile($fullpath, $name);
                if (!str_contains($image->getMimeType(), 'image/')) {
                    throw new \Exception("ERROR: {$imageFile} is not an image file.");
                }
                array_push($images, $image);
            }

            $data = json_decode($dataFile, associative: true);
            $validator = Validator::make($data, [
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

            if ($validator->fails()) {
                $errorMessage = "{$immobilieFolder}/data.json contains invalid immobilie data: ";
                $errorMessage = $errorMessage.($validator->errors());
                throw new \Exception($errorMessage);
            }

            $immobilie = [];
            $immobilie['data'] = $data;
            $immobilie['images'] = $images;
            array_push($immobilien, $immobilie);
        }

        foreach($immobilien as $immobilie) {
            $title = Translation::create($immobilie['data']['title']);
            $short = Translation::create($immobilie['data']['short_description']);
            $long = Translation::create($immobilie['data']['long_description']);

            $stateId = DB::table('states')->where(['state' => $immobilie['data']['state']])->first()->id;
            $location = Location::firstorCreate(['city'=>$immobilie['data']['city'], 'state_id' => $stateId]);
            $hidden = $immobilie['data']['hidden']??false;

            $immobilieRecord = Immobilie::create([
                'title_id'             => $title->id,
                'short_description_id' => $short->id,
                'long_description_id'  => $long->id,
                'address'           => $immobilie['data']['address'],
                'zip'               => $immobilie['data']['zip'],
                'location_id'          => $location->id,
                'year'              => $immobilie['data']['year'],
                'type'              => $immobilie['data']['type'],
                'usage'             => $immobilie['data']['usage'],
                'hidden'            => $hidden
            ]);

            foreach ($immobilie['data']['units'] as $unit) {
                $type = UnitType::firstOrCreate(['type' => $unit['type']]);
                $immobilieRecord->units()->create([
                    'price' => $unit['price'],
                    'rent'  => $unit['rent'],
                    'rooms' => $unit['rooms'],
                    'area'  => $unit['area'],
                    'type_id'  => $type->id
                ]);
            }

            foreach($immobilie['images'] as $image) {
                $imageRecord = new Image();
                $imageRecord->file = $image;
                $imageRecord->owner()->associate($immobilieRecord);
                $imageRecord->save();
            }
        }

    }
}
