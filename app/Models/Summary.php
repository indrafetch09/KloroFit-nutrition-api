<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $table = 'summaries';

    protected $fillable = [
        'user_id',
        'type',
        'date',
        'calories',
        'fat',
        'protein',
        'carbs',
        'activity_calories',
        'activity_minutes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalCaloriesAttribute()
    {
        return $this->calories + $this->activity_calories;
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
