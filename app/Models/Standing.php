<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Standing extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['season_id', 'team_id', 'played', 'won', 'drawn', 'lost', 'goals_for', 'goals_against', 'goal_difference', 'points'];

    public function season(): BelongsTo {
        return $this->belongsTo(Season::class);
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    // Helper Function to Recalculate Totals
    public function updateCalculatedStats(int $gf, int $ga, string $result): void {
        $this->played += 1;
        $this->goals_for += $gf;
        $this->goals_against += $ga;
        $this->goal_difference = $this->goals_for - $this->goals_against;

        if ($result === 'WIN') {
            $this->won += 1;
            $this->points += 3;
        } elseif ($result === 'DRAW') {
            $this->drawn += 1;
            $this->points += 1;
        } else {
            $this->lost += 1;
        }

        $this->save();
    }
}