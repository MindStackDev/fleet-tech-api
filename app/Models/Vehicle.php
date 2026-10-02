<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'vehicle_type_id',
        'plate_number',
        'vin',
        'brand',
        'model',
        'year',
        'current_mileage',
        'status',
        'specs',
    ];

    protected function casts(): array
    {
        return [
            'status' => VehicleStatus::class,
            'specs' => 'array',
            'current_mileage' => 'integer',
            'year' => 'integer',
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
     * @return BelongsTo<VehicleType, $this>
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * @return HasMany<Inspection, $this>
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    /**
     * @return HasMany<Defect, $this>
     */
    public function defects(): HasMany
    {
        return $this->hasMany(Defect::class);
    }
}
