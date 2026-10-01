<?php

declare(strict_types=1);

namespace App\Enums;

enum VehicleStatus: string
{
    case ACTIVE = 'active';
    case IN_MAINTENANCE = 'in_maintenance';
    case OUT_OF_SERVICE = 'out_of_service';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Operativo en Patio',
            self::IN_MAINTENANCE => 'En Mantenimiento / Bloqueado',
            self::OUT_OF_SERVICE => 'Fuera de Servicio',
        };
    }
}
