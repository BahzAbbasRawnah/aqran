<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'chat_link',
        'semester_end_date',
        'expires_at',
    ];

    protected $casts = [
        'semester_end_date' => 'date',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the members of the project.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the files for the project.
     */
    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }
}
