<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerStatSummary extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['season_id', 'player_id', 'appearances', 'goals', 'assists', 'yellow_cards', 'red_cards', 'clean_sheets'];

    public function season(): BelongsTo {
        return $this->belongsTo(Season::class);
    }

    public function player(): BelongsTo {
        return $this->belongsTo(Player::class);
    }
}