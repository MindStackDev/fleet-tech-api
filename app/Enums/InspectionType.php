<?php

declare(strict_types=1);

namespace App\Enums;

enum InspectionType: string
{
    case SHIFT_HANDOVER = 'shift_handover';
    case ROUTINE_CHECK = 'routine_check';
    case FAULT_DIAGNOSIS = 'fault_diagnosis';
    case POST_REPAIR = 'post_repair';

    public function label(): string
    {
        return match ($this) {
            self::SHIFT_HANDOVER => 'Relevo de Turno',
            self::ROUTINE_CHECK => 'Inspección Rutinaria de Patio',
            self::FAULT_DIAGNOSIS => 'Diagnóstico de Falla',
            self::POST_REPAIR => 'Verificación Post-Reparación',
        };
    }
}
