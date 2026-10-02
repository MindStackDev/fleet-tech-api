<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ItemInputType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'label',
        'description',
        'input_type',
        'options',
        'is_mandatory',
        'requires_photo_on_fail',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'input_type' => ItemInputType::class,
            'options' => 'array',
            'is_mandatory' => 'boolean',
            'requires_photo_on_fail' => 'boolean',
            'order_index' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<ChecklistSection, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ChecklistSection::class, 'section_id');
    }
}
