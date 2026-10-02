<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShiftType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ChecklistTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_type_id',
        'title',
        'description',
        'shift_type',
        'version',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'shift_type' => ShiftType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<VehicleType, $this>
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * @return HasMany<ChecklistSection, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ChecklistSection::class)->orderBy('order_index');
    }

    /**
     * @return HasMany<Inspection, $this>
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
