<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    public $timestamps = false;

    protected $table = 'user_activities';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'activity_date',
        'duration_minutes',
        'distance',
        'calories_burned',
        'created_at',
    ];


    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
