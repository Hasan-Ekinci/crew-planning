<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shift extends Model
{
    protected $fillable = ['title', 'description', 'shift_role', 'max_crew', 'start_datetime', 'end_datetime'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_shifts');
    }
}
