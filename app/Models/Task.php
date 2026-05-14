<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'due_datetime',
        'is_completed',
    ];

    protected $casts = [
        'due_datetime' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the student that owns the task.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the reminders for the task.
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(TaskReminder::class);
    }
}
