<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AnswerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class InspectionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'checklist_item_id',
        'status',
        'value',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => AnswerStatus::class,
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
        return $this->hasMany(Defect::class, 'checklist_item_id', 'checklist_item_id')
            ->where('inspection_id', $this->inspection_id);
    }
}
