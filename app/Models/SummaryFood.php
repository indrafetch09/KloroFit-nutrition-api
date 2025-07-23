<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryFood extends Model
{
    protected $table = 'summaries_foods';

    protected $fillable = [
        'user_id',
        'date',
        'calories',
        'fat',
        'protein',
        'carbs',
        'breakfast_calories',
        'lunch_calories',
        'dinner_calories',
        'snack_calories',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalFatAttribute()
    {
        return $this->fat;
    }

    public function getTotalProteinAttribute()
    {
        return $this->protein;
    }

    public function getTotalCarbsAttribute()
    {
        return $this->carbs;
    }
}
