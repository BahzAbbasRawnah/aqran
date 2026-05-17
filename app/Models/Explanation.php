<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Explanation extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'video_path',
        'thumbnail_path',
        'views',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full URL for video.
     */
    public function getVideoUrlAttribute()
    {
        if (!$this->video_path) return null;
        if (str_starts_with($this->video_path, 'http')) return $this->video_path;
        
        $cleanPath = ltrim($this->video_path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        
        return \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPath);
    }

    /**
     * Get full URL for thumbnail.
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) return null;
        if (str_starts_with($this->thumbnail_path, 'http')) return $this->thumbnail_path;
        
        $cleanPath = ltrim($this->thumbnail_path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        
        return \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPath);
    }

    protected $appends = ['video_url', 'thumbnail_url'];
}
