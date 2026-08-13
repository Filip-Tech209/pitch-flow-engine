<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'city', 'capacity', 'latitude', 'longitude'];

    public function matches(): HasMany {
        return $this->hasMany(GameMatch::class, 'venue_id');
    }

    public function homeTeams(): HasMany {
        return $this->hasMany(Team::class, 'home_venue_id');
    }
}