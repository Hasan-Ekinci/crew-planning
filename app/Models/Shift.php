<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['title', 'description', 'shift_role', 'max_crew', 'start_datetime', 'end_datetime'];
}
