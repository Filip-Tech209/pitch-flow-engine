<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerRegistration extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['season_id', 'team_id', 'player_id', 'jersey_number', 'position', 'height_cm', 'weight_kg'];

    public function season(): BelongsTo {
        return $this->belongsTo(Season::class);
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo {
        return $this->belongsTo(Player::class);
    }
}