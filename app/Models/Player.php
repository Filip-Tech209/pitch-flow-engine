<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['first_name', 'last_name', 'dob', 'photo_path', 'national_id_passport', 'nationality', 'foot'];

    protected $casts = ['dob' => 'date'];

    public function registrations(): HasMany {
        return $this->hasMany(PlayerRegistration::class);
    }

    public function events(): HasMany {
        return $this->hasMany(MatchEvent::class);
    }

    public function stats(): HasMany {
        return $this->hasMany(PlayerStatSummary::class);
    }

    // Helper Function
    public function getFullNameAttribute(): string {
        return "{$this->first_name} {$this->last_name}";
    }
}