<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchOfficial extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['match_id', 'official_id', 'assigned_role'];

    public function match(): BelongsTo {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function official(): BelongsTo {
        return $this->belongsTo(Official::class);
    }
}