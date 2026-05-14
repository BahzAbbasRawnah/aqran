<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'reminder_type',
        'is_sent',
    ];

    protected $casts = [
        'reminder_type' => 'string',
        'is_sent' => 'boolean',
    ];

    /**
     * Get the task that owns the reminder.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
