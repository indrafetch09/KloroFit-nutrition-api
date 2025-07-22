<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'calories',
        'carbs',
        'protein',
        'fat'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
