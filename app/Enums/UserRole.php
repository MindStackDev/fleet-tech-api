<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SUPERVISOR = 'supervisor';
    case OPERATOR = 'operator';
    case TECHNICIAN = 'technician';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::SUPERVISOR => 'Supervisor de Patio',
            self::OPERATOR => 'Operador de Turno',
            self::TECHNICIAN => 'Técnico Mecánico',
        };
    }
}
