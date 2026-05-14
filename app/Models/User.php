<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'birth_date',
        'auth_provider',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birth_date'        => 'date',
            'auth_provider'     => 'string',
            'role'              => 'string',
        ];
    }

    /**
     * Grant admin-role users access to the Filament Admin Panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the student profile associated with the user.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the projects the user is a member of.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the files uploaded by the user.
     */
    public function projectFiles(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    /**
     * Get the communities the user is a member of.
     */
    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class)->withTimestamps()->withPivot('joined_at');
    }

    /**
     * Get the workshops created by the user.
     */
    public function workshops(): HasMany
    {
        return $this->hasMany(Workshop::class);
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute(): string
    {
        $name = trim("{$this->first_name} {$this->last_name}");
        return $name ?: ($this->username ?? 'User');
    }

    /**
     * Get the announcements created by the user.
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
