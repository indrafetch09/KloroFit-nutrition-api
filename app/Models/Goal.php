<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Goal extends Model
{
    protected $table = 'goals';

    protected $fillable = [
        'user_id',
        'date',
        'calories',
        'carbs',
        'protein',
        'fat',
        'created_at',
        'updated_at',
    ];
}
