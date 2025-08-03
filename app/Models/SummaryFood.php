<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryFood extends Model
{
    protected $table = 'summaries_foods';

    protected $fillable = [
        'user_id',
        'date',
        'total_calories',
        'total_fat',
        'total_protein',
        'total_carbs',
    ];

    protected $casts = [
        'date' => 'date',
        'total_calories' => 'float',
        'total_fat' => 'float',
        'total_protein' => 'float',
        'total_carbs' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
