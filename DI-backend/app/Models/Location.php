<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    protected string $guard_name = 'api';
    public $timestamps = false;
    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }

    protected $fillable = [
        'city',
        'state_id'
    ];
    
    protected $appends = ['state'];

    protected function state(): Attribute
    {
        return Attribute::make(
            get: fn () => DB::table('states')->find($this->state_id)->state,
        );
    }

    public function immobilien(): HasMany
    {
        return $this->hasMany(Immobilie::class, 'location_id');
    }
}
