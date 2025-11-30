<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected string $guard_name = 'api';
    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }

    protected $fillable = [
        'price',
        'rent',
        'rooms',
        'area',
        'type_id'
    ];

    protected $appends = ['type'];

    protected $guarded = [];

    public function type(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'type_id');
    }

    public function getTypeAttribute() {
        return ucfirst($this->type()->value('type'));
    }

    public function immobilie(): BelongsTo
    {
        return $this->belongsTo(Immobilie::class, 'immobilie_id');
    }

    protected static function booted()
    {

        static::deleting(function (Unit $unit) {
            $immobilie = $unit->immobilie()->withCount('units')->first();
            if ($immobilie && $immobilie->units_count <= 1) {
                throw new \Exception("Cannot delete the last unit of an Immobilie.");
            }
        });
    }
}
