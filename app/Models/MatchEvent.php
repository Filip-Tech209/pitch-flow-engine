<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['match_id', 'team_id', 'player_id', 'related_player_id', 'minute', 'extra_minute', 'event_type', 'notes'];

    public function match(): BelongsTo {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function relatedPlayer(): BelongsTo {
        return $this->belongsTo(Player::class, 'related_player_id');
    }
}