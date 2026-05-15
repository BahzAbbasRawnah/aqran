<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'major_id',
        'name',
        'description',
        'category',
        'join_link',
        'cover_image',
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'status' => \App\Enums\ContentStatus::class,
    ];

    /**
     * Get the user that created the community.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * The members that belong to the community.
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('joined_at');
    }

    /**
     * Get the workshops for the community.
     */
    public function workshops()
    {
        return $this->hasMany(Workshop::class);
    }

    /**
     * Get the announcements for the community.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}
