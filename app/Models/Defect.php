<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DefectSeverity;
use App\Enums\DefectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Defect extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'checklist_item_id',
        'vehicle_id',
        'severity',
        'status',
        'description',
        'reported_by',
        'resolved_by',
        'resolution_notes',
        'resolved_evidence_path',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'severity' => DefectSeverity::class,
            'status' => DefectStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Inspection, $this>
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    /**
     * @return BelongsTo<ChecklistItem, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isCritical(): bool
    {
        return $this->severity === DefectSeverity::CRITICAL;
    }

    public function isResolved(): bool
    {
        return $this->status === DefectStatus::RESOLVED;
    }
}
