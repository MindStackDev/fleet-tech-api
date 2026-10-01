<?php

declare(strict_types=1);

namespace App\Enums;

enum ShiftType: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case NIGHT = 'night';
    case ANY = 'any';

    public function label(): string
    {
        return match ($this) {
            self::MORNING => 'Turno Mañana',
            self::AFTERNOON => 'Turno Tarde',
            self::NIGHT => 'Turno Noche',
            self::ANY => 'Cualquier Turno',
        };
    }
}
