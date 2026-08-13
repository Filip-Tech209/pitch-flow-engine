<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonTeam extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['season_id', 'team_id', 'group_name'];

    public function season(): BelongsTo {
        return $this->belongsTo(Season::class);
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }
}