<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_id',
        'name',
        'effective_year',
    ];

    protected $casts = [
        'effective_year' => 'integer',
    ];

    /**
     * Get the major that owns the study plan.
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get the courses associated with the study plan.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'study_plan_courses')
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
     * Get the students following this study plan.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
