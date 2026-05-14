<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'is_pinned',
        'file_path',
        'file_name',
        'file_size_mb',
        'description',
    ];

    protected $casts = [
        'file_size_mb' => 'decimal:2',
        'is_pinned' => 'boolean',
    ];

    /**
     * Get the uploader of the file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the project that owns the file.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user that uploaded the file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
