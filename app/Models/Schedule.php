<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'semester',
        'year',
    ];

    protected $casts = [
        'semester' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Get the student that owns the schedule.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the items for the schedule.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ScheduleItem::class);
    }
}
