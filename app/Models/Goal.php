<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Goal extends Model
{
    protected $table = 'goals';

    protected $fillable = [
        'user_id',
        'calories',
        'carbs',
        'protein',
        'fat',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
