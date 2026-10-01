<?php

declare(strict_types=1);

namespace App\Enums;

enum AnswerStatus: string
{
    case PASS = 'pass';
    case FAIL = 'fail';
    case NA = 'na';

    public function label(): string
    {
        return match ($this) {
            self::PASS => 'Conforme / Operativo',
            self::FAIL => 'Falla / Faltante / Dañado',
            self::NA => 'No Aplica',
        };
    }
}
