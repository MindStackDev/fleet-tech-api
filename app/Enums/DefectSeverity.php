<?php

declare(strict_types=1);

namespace App\Enums;

enum DefectSeverity: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Leve (Observación estética o menor)',
            self::MEDIUM => 'Medio (Requiere atención de taller)',
            self::CRITICAL => 'Crítico (Inhabilita la unidad / Bloqueo)',
        };
    }
}
