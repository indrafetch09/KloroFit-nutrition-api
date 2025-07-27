<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'calories',
        'carbs',
        'protein',
        'fat',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
