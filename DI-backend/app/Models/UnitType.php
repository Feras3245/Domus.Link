<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitType extends Model
{
    protected $fillable = ['type'];
    protected $table = 'unit_types';
    public $timestamps = false;

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'type_id');
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value)
        );
    }
}
