<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'target_audience_major_id',
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'status' => \App\Enums\ContentStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the majors that this workshop targets.
     */
    public function targetMajors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Major::class, 'workshop_majors');
    }

    /**
     * Get the user that created the workshop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the major that this workshop targets.
     */
    public function targetMajor(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'target_audience_major_id');
    }
}
