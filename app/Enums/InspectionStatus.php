<?php

declare(strict_types=1);

namespace App\Enums;

enum InspectionStatus: string
{
    case DRAFT = 'draft';
    case COMPLETED = 'completed';
    case FLAGGED = 'flagged';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::COMPLETED => 'Completada / Sin Novedad',
            self::FLAGGED => 'Con Observaciones / Defectos',
            self::APPROVED => 'Aprobada por Supervisor',
            self::REJECTED => 'Rechazada',
        };
    }
}
