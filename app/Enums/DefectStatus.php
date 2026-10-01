<?php

declare(strict_types=1);

namespace App\Enums;

enum DefectStatus: string
{
    case OPEN = 'open';
    case IN_WORKSHOP = 'in_workshop';
    case RESOLVED = 'resolved';
    case DISMISSED = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Abierto / Pendiente',
            self::IN_WORKSHOP => 'En Taller / En Reparación',
            self::RESOLVED => 'Subsanado / Reparado',
            self::DISMISSED => 'Desestimado por Supervisión',
        };
    }
}
