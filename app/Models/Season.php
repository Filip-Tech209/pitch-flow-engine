<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <--- ADDED THIS IMPORT
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['competition_id', 'name', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function competition(): BelongsTo 
    {
        return $this->belongsTo(Competition::class);
    }

    public function seasonTeams(): HasMany 
    {
        return $this->hasMany(SeasonTeam::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'season_teams', 'season_id', 'team_id');
    }

    public function matches(): HasMany 
    {
        return $this->hasMany(GameMatch::class);
    }

    public function standings(): HasMany 
    {
        return $this->hasMany(Standing::class)->orderBy('points', 'desc')->orderBy('goal_difference', 'desc');
    }
}