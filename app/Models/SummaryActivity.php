<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryActivity extends Model
{
    protected $table = 'summaries_activities';

    protected $fillable = [
        'user_id',
        'date',
        'calories_burned',
        'duration_minutes',
        'activity_count',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
