<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<Inspection, $this>
     */
    public function performedInspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'operator_id');
    }

    /**
     * @return HasMany<Inspection, $this>
     */
    public function receivedInspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'receiving_operator_id');
    }

    /**
     * @return HasMany<Defect, $this>
     */
    public function reportedDefects(): HasMany
    {
        return $this->hasMany(Defect::class, 'reported_by');
    }

    /**
     * @return HasMany<Defect, $this>
     */
    public function resolvedDefects(): HasMany
    {
        return $this->hasMany(Defect::class, 'resolved_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isSupervisor(): bool
    {
        return $this->role === UserRole::SUPERVISOR;
    }

    public function isOperator(): bool
    {
        return $this->role === UserRole::OPERATOR;
    }

    public function isTechnician(): bool
    {
        return $this->role === UserRole::TECHNICIAN;
    }
}
