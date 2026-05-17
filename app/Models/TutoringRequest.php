<?php

namespace App\Models;

use App\Models\Explanation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutoringRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'is_completed',
        'grade',
        'learning_details',
        'curriculum_parts',
        'video_url',      // Canonical column name (matches DB schema)
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'status' => \App\Enums\ContentStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the student who made the request.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for which tutoring is requested.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get full URL for demo video.
     */
    public function getVideoUrlAttribute($value)
    {
        if (!$value) return null;

        // If it already has http, return as-is
        if (str_starts_with($value, 'http')) return $value;

        // Strip any leading /storage/ prefix (legacy records)
        $cleanPath = ltrim($value, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPath);
    }
}
