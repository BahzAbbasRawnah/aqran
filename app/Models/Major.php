<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the study plans for the major.
     */
    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    /**
     * Get the courses for the major.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the students for the major.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the workshops targeting this major.
     */
    public function workshops(): HasMany
    {
        return $this->hasMany(Workshop::class, 'target_audience_major_id');
    }
}
