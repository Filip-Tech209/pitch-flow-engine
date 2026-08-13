<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['home_venue_id', 'name', 'short_name', 'logo_path', 'coach_name', 'primary_color', 'secondary_color', 'default_formation'];

    public function homeVenue(): BelongsTo {
        return $this->belongsTo(Venue::class, 'home_venue_id');
    }

    public function seasonRegistrations(): HasMany {
        return $this->hasMany(SeasonTeam::class);
    }

    public function playerRegistrations(): HasMany {
        return $this->hasMany(PlayerRegistration::class);
    }

    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class, 'season_team', 'team_id', 'season_id');
    }

    public function homeMatches(): HasMany {
        return $this->hasMany(GameMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany {
        return $this->hasMany(GameMatch::class, 'away_team_id');
    }
}