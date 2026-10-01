<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemInputType: string
{
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
    case NUMBER = 'number';
    case TEXT = 'text';

    public function label(): string
    {
        return match ($this) {
            self::BOOLEAN => 'Conforme / Falla (Sí / No)',
            self::SELECT => 'Selección Única',
            self::NUMBER => 'Valor Numérico',
            self::TEXT => 'Texto Libre',
        };
    }
}
