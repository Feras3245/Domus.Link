<?php

namespace App\Models;

use Arr;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\Storage;

class Immobilie extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected string $guard_name = 'api';
    protected $table = 'immobilien';

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }

    public const TYPE_OPTIONS = ['NEUBAU', 'BESTAND'];
    public const USAGE_OPTIONS = ['WOHNEN', 'MICRO', 'PFLEGE'];

    protected function hidden(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value >= 1,
        );
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'title_id');
    }

    public function shortDescription(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'short_description_id');
    }

    public function longDescription(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'long_description_id');
    }
    
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'owner');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'immobilie_id');
    }

    protected function stats(): Attribute
    {
        return Attribute::make(
            get: function () {
                $units = $this->units()->get(['price', 'rent', 'yield', 'rooms', 'area']);
                return [
                    'price' => [
                        'min' => $units->min('price'),
                        'max' => $units->max('price')
                    ],
                    'rent' => [
                        'min' => $units->min('rent'),
                        'max' => $units->max('rent')
                    ],
                    'yield' => [
                        'min' => $units->min('yield'),
                        'max' => $units->max('yield')
                    ],
                    'rooms' => [
                        'min' => $units->min('rooms'),
                        'max' => $units->max('rooms'),
                    ],
                    'area' => [
                        'min' => $units->min('area'),
                        'max' => $units->max('area')
                    ]
                ];
            },
        );
    }

    protected static function booted(){
        static::deleting(function (Immobilie $immobilie) {

            $immobilie->images()->delete();
            if (Storage::disk('local')->exists("/images/immobilien/{$immobilie->id}")) {
                Storage::disk('local')->deleteDirectory("/images/immobilien/{$immobilie->id}");
            }

            $immobilie->title()->delete();
            $immobilie->shortDescription()->delete();
            $immobilie->longDescription()->delete();

            $count = $immobilie->location()->withCount('immobilien')->value('immobilien_count');
            if ($count <= 1) {
                $immobilie->location()->delete();
            }
        });
    }
}
