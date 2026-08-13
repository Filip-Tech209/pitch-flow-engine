<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchLineup extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['match_id', 'team_id', 'player_id', 'jersey_number', 'position', 'is_starter', 'is_captain'];

    protected $casts = [
        'is_starter' => 'boolean',
        'is_captain' => 'boolean',
    ];

    public function match(): BelongsTo {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo {
        return $this->belongsTo(Player::class);
    }
}