<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_id',
        'code',
        'name',
    ];

    /**
     * Get the major that owns the course.
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get the study plans that include this course.
     */
    public function studyPlans(): BelongsToMany
    {
        return $this->belongsToMany(StudyPlan::class, 'study_plan_courses')
            ->withPivot('id', 'semester_level', 'course_type')
            ->withTimestamps();
    }

    /**
     * Get the study plan courses entries.
     */
    public function studyPlanCourses(): HasMany
    {
        return $this->hasMany(StudyPlanCourse::class);
    }

    /**
     * Get the schedule items for this course.
     */
    public function scheduleItems(): HasMany
    {
        return $this->hasMany(ScheduleItem::class);
    }

    /**
     * Get the explanations for this course.
     */
    public function explanations(): HasMany
    {
        return $this->hasMany(Explanation::class);
    }
}
