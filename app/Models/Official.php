<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Official extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['first_name', 'last_name', 'role', 'nationality'];

    public function matchAssignments(): HasMany {
        return $this->hasMany(MatchOfficial::class);
    }

    // Helper Function
    public function getFullNameAttribute(): string {
        return "{$this->first_name} {$this->last_name}";
    }
}