<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'course_id',
        'type',
        'day_of_week',
        'start_time',
        'end_time',
        'notes',
    ];

    protected $casts = [
        'type' => 'string',
        'day_of_week' => 'string',
        'start_time' => 'string', // time is usually string or cast to Carbon if needed
        'end_time' => 'string',
    ];

    /**
     * Get the schedule that owns the item.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the course associated with the item.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
