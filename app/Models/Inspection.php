<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\ShiftType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'checklist_template_id',
        'inspection_type',
        'shift',
        'operator_id',
        'receiving_operator_id',
        'start_mileage',
        'end_mileage',
        'fuel_level',
        'yard_location',
        'latitude',
        'longitude',
        'status',
        'operator_signature_path',
        'receiving_signature_path',
        'general_notes',
        'inspected_at',
    ];

    protected function casts(): array
    {
        return [
            'inspection_type' => InspectionType::class,
            'shift' => ShiftType::class,
            'status' => InspectionStatus::class,
            'start_mileage' => 'integer',
            'end_mileage' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'inspected_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<ChecklistTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ChecklistTemplate::class, 'checklist_template_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function receivingOperator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiving_operator_id');
    }

    /**
     * @return HasMany<InspectionAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(InspectionAnswer::class);
    }

    /**
     * @return HasMany<InspectionEvidence, $this>
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(InspectionEvidence::class);
    }

    /**
     * @return HasMany<Defect, $this>
     */
    public function defects(): HasMany
    {
        return $this->hasMany(Defect::class);
    }
}
