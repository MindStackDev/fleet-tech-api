<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InspectionEvidence extends Model
{
    use HasFactory;

    protected $table = 'inspection_evidences';

    protected $fillable = [
        'inspection_id',
        'inspection_answer_id',
        'file_path',
        'caption',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
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
     * @return BelongsTo<InspectionAnswer, $this>
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(InspectionAnswer::class, 'inspection_answer_id');
    }
}
