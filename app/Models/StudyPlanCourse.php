<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPlanCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_plan_id',
        'course_id',
        'semester_level',
        'course_type',
    ];

    protected $casts = [
        'semester_level' => 'integer',
        'course_type' => 'string',
    ];

    /**
     * Get the study plan that owns the entry.
     */
    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    /**
     * Get the course that owns the entry.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
