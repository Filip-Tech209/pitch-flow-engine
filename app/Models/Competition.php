<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'code', 'type', 'logo_path', 'status', 'start_date', 'end_date'];

    public function seasons(): HasMany {
        return $this->hasMany(Season::class);
    }

    public function activeSeason() {
        return $this->hasOne(Season::class)->where('is_active', true);
    }
}