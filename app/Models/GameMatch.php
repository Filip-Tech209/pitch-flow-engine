<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameMatch extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'matches';

   protected $fillable = [
        'season_id',
        'home_team_id',
        'away_team_id',
        'venue_id',
        'kickoff_time',
        'status',
        'home_score',
        'away_score',
    ];

    protected $casts = ['kickoff_time' => 'datetime'];

    public function season(): BelongsTo {
        return $this->belongsTo(Season::class);
    }

    public function homeTeam(): BelongsTo {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function venue(): BelongsTo {
        return $this->belongsTo(Venue::class);
    }

    public function lineups(): HasMany {
        return $this->hasMany(MatchLineup::class, 'match_id');
    }

    public function events(): HasMany {
        return $this->hasMany(MatchEvent::class, 'match_id')->orderBy('minute', 'asc');
    }

    public function officials(): HasMany {
        return $this->hasMany(MatchOfficial::class, 'match_id');
    }

    // Helper function for match summary
    public function isLive(): bool {
        return in_array($this->status, ['LIVE_1ST_HALF', 'HALF_TIME', 'LIVE_2ND_HALF']);
    }
}