<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_url',
        'publish_date',
        'expires_at',
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'expires_at' => 'datetime',
        'status' => \App\Enums\ContentStatus::class,
    ];

    /**
     * Get the user that created the announcement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the majors that this announcement targets.
     */
    public function majors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Major::class, 'announcement_majors');
    }
}
