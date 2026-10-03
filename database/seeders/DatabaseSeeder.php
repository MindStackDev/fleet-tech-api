<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ItemInputType;
use App\Enums\ShiftType;
use App\Enums\UserRole;
use App\Enums\VehicleStatus;
use App\Models\ChecklistItem;
use App\Models\ChecklistSection;
use App\Models\ChecklistTemplate;
use App\Models\Company;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Empresa de Transporte / Logística
        $company = Company::create([
            'name' => 'TransAndina Logistics S.A.C.',
            'tax_id' => '20601234567',
            'contact_phone' => '+51 987654321',
            'contact_email' => 'operaciones@transandina.pe',
            'is_active' => true,
        ]);

        // 2. Usuarios con roles operativos
        User::create([
            'company_id' => $company->id,
            'name' => 'Administrador Central',
            'email' => 'admin@fleettech.pe',
            'password' => Hash::make('password123'),
            'role' => UserRole::ADMIN,
            'phone' => '+51 999111000',
            'is_active' => true,
        ]);

        $supervisor = User::create([
            'company_id' => $company->id,
            'name' => 'Carlos Mendoza (Jefe de Patio)',
            'email' => 'supervisor@fleettech.pe',
            'password' => Hash::make('password123'),
            'role' => UserRole::SUPERVISOR,
            'phone' => '+51 999222000',
            'is_active' => true,
        ]);

        $operadorManana = User::create([
            'company_id' => $company->id,
            'name' => 'Juan Quispe (Operador Mañana)',
            'email' => 'operador.manana@fleettech.pe',
            'password' => Hash::make('password123'),
            'role' => UserRole::OPERATOR,
            'phone' => '+51 999333000',
            'is_active' => true,
        ]);

        $operadorTarde = User::create([
            'company_id' => $company->id,
            'name' => 'Mario Flores (Operador Tarde)',
            'email' => 'operador.tarde@fleettech.pe',
            'password' => Hash::make('password123'),
            'role' => UserRole::OPERATOR,
            'phone' => '+51 999444000',
            'is_active' => true,
        ]);

        $mecanico = User::create([
            'company_id' => $company->id,
            'name' => 'Roberto Sánchez (Técnico Mecánico)',
            'email' => 'mecanico@fleettech.pe',
            'password' => Hash::make('password123'),
            'role' => UserRole::TECHNICIAN,
            'phone' => '+51 999555000',
            'is_active' => true,
        ]);

        // 3. Tipos de Vehículo
        $tracto = VehicleType::create([
            'name' => 'Tractocamión 6x4',
            'description' => 'Unidad tractora pesada para remolques y plataformas',
        ]);

        $cisterna = VehicleType::create([
            'name' => 'Camión Cisterna',
            'description' => 'Unidad para transporte de combustible o líquidos industriales',
        ]);

        $camioneta = VehicleType::create([
            'name' => 'Camioneta 4x4 Patio',
            'description' => 'Vehículo de supervisión, auxilio mecánico y escolta',
        ]);

        // 4. Padrón Vehicular de Patio con Ficha Técnica (JSONB Specs)
        Vehicle::create([
            'company_id' => $company->id,
            'vehicle_type_id' => $tracto->id,
            'plate_number' => 'ABC-101',
            'vin' => '9BWKB45U0PB123456',
            'brand' => 'Volvo',
            'model' => 'FH 540 Globetrotter',
            'year' => 2024,
            'current_mileage' => 45200,
            'status' => VehicleStatus::ACTIVE,
            'specs' => [
                'motor' => 'D13K 540 HP Euro 5',
                'transmision' => 'I-Shift 12 velocidades automatizada',
                'tanque_combustible' => '650 Litros Diésel',
                'capacidad_arrastre' => '60 Toneladas',
                'configuracion_ejes' => '6x4 con traba interaxial',
                'bahia_asignada' => 'Patio Norte - Bahía 04',
            ],
        ]);

        Vehicle::create([
            'company_id' => $company->id,
            'vehicle_type_id' => $tracto->id,
            'plate_number' => 'XYZ-202',
            'vin' => 'YS2R4X20005432100',
            'brand' => 'Scania',
            'model' => 'R500 Highline',
            'year' => 2023,
            'current_mileage' => 78400,
            'status' => VehicleStatus::ACTIVE,
            'specs' => [
                'motor' => 'DC13 500 HP Euro 5',
                'transmision' => 'Opticruise con Retarder',
                'tanque_combustible' => '500 Litros Diésel',
                'capacidad_arrastre' => '55 Toneladas',
                'configuracion_ejes' => '6x4',
                'bahia_asignada' => 'Patio Sur - Bahía 12',
            ],
        ]);

        Vehicle::create([
            'company_id' => $company->id,
            'vehicle_type_id' => $camioneta->id,
            'plate_number' => 'PAT-303',
            'vin' => '8AJBA3CD401987654',
            'brand' => 'Toyota',
            'model' => 'Hilux SRV 4x4',
            'year' => 2025,
            'current_mileage' => 11500,
            'status' => VehicleStatus::ACTIVE,
            'specs' => [
                'motor' => '2.8L Turbo Diésel 204 HP',
                'transmision' => 'Automática 6 vel con 4x4 reductora',
                'equipamiento_patio' => 'Barra antivuelco, circulina LED, pértiga 3m, caja de herramientas',
                'bahia_asignada' => 'Estacionamiento Supervisión 01',
            ],
        ]);

        // 5. Plantilla Dinámica Maestra: Inspección de Patio y Relevo de Turno
        $template = ChecklistTemplate::create([
            'vehicle_type_id' => null,
            'title' => 'Inspección de Patio y Relevo de Turno',
            'description' => 'Checklist obligatorio para cambio de turno entre operadores. Audita estado de unidad, fluidos, llantas y kit de herramientas completas dotadas por la empresa.',
            'shift_type' => ShiftType::ANY,
            'version' => '1.0',
            'is_active' => true,
        ]);

        // Sección 1: Documentación de la Unidad
        $sec1 = ChecklistSection::create([
            'checklist_template_id' => $template->id,
            'title' => '1. Documentación Obligatoria a Bordo',
            'order_index' => 1,
        ]);

        ChecklistItem::create([
            'section_id' => $sec1->id,
            'label' => 'Tarjeta de Propiedad Vehicular física o laminada',
            'description' => 'Verificar que coincida número de placa y chasis',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 1,
        ]);

        ChecklistItem::create([
            'section_id' => $sec1->id,
            'label' => 'Póliza de SOAT digital vigente',
            'description' => 'Comprobar fecha de vencimiento activa en sistema',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 2,
        ]);

        ChecklistItem::create([
            'section_id' => $sec1->id,
            'label' => 'Certificado CITV (Inspección Técnica Vehicular)',
            'description' => 'Certificado vigente de transporte de carga',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 3,
        ]);

        // Sección 2: Fluidos, Frenos y Luces
        $sec2 = ChecklistSection::create([
            'checklist_template_id' => $template->id,
            'title' => '2. Estado Mecánico, Fluidos y Luces en Patio',
            'order_index' => 2,
        ]);

        ChecklistItem::create([
            'section_id' => $sec2->id,
            'label' => 'Nivel de Aceite de Motor y ausencia de fugas',
            'description' => 'Medir con varilla en frío o tablero digital',
            'input_type' => ItemInputType::SELECT,
            'options' => ['Normal (Nivel Óptimo)', 'Nivel Bajo (Requiere relleno)', 'Crítico / Fuga Activa'],
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 1,
        ]);

        ChecklistItem::create([
            'section_id' => $sec2->id,
            'label' => 'Sistema de Frenos y Presión Neumática de Aire',
            'description' => 'Sin caídas bruscas de presión en tanques y freno de parqueo operativo',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 2,
        ]);

        ChecklistItem::create([
            'section_id' => $sec2->id,
            'label' => 'Faros Principales, Neblineros y Luces de Freno / Direccionales',
            'description' => 'Todas las bombillas y módulos LED operativos',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 3,
        ]);

        // Sección 3: Kit de Herramientas y Seguridad a Bordo (Dotación Empresa)
        $sec3 = ChecklistSection::create([
            'checklist_template_id' => $template->id,
            'title' => '3. Kit de Herramientas y Equipamiento a Bordo (Dotación Empresa)',
            'order_index' => 3,
        ]);

        ChecklistItem::create([
            'section_id' => $sec3->id,
            'label' => 'Gata Hidráulica de Botella (20T / Capacidad según tonelaje) con Palanca',
            'description' => 'Verificar que suba sin fugas de aceite y palanca completa',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 1,
        ]);

        ChecklistItem::create([
            'section_id' => $sec3->id,
            'label' => 'Llave de Ruedas tipo Cruz o Telescópica con Dado Correspondiente',
            'description' => 'Bocas sin desgaste ni fisuras para desmontaje de ruedas',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 2,
        ]);

        ChecklistItem::create([
            'section_id' => $sec3->id,
            'label' => 'Juego de 2 Triángulos de Seguridad y 2 Conos Reflectivos (28")',
            'description' => 'Completos y con bandas reflectivas limpias',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 3,
        ]);

        ChecklistItem::create([
            'section_id' => $sec3->id,
            'label' => 'Extintor PQS 6kg con Manómetro en Verde y Precinto Intacto',
            'description' => 'Fecha de vencimiento vigente y manguera en buen estado',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 4,
        ]);

        ChecklistItem::create([
            'section_id' => $sec3->id,
            'label' => 'Botiquín de Primeros Auxilios Reglamentario Equipado',
            'description' => 'Insumos vigentes y maletín sellado',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 5,
        ]);

        // Sección 4: Herramientas de Trabajo del Operador de Patio
        $sec4 = ChecklistSection::create([
            'checklist_template_id' => $template->id,
            'title' => '4. Herramientas de Trabajo del Operador de Patio (Relevo)',
            'order_index' => 4,
        ]);

        ChecklistItem::create([
            'section_id' => $sec4->id,
            'label' => 'Escáner de Diagnóstico Automotriz (OBD II) Operativo y con Batería',
            'description' => 'Se transfiere en mano entre operadores entrante y saliente',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 1,
        ]);

        ChecklistItem::create([
            'section_id' => $sec4->id,
            'label' => 'Linterna Recargable de Inspección Nocturna de Alta Potencia',
            'description' => 'Operativa y probada al momento del cambio de turno',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 2,
        ]);

        ChecklistItem::create([
            'section_id' => $sec4->id,
            'label' => 'Medidor Digital de Presión y Profundímetro de Neumáticos',
            'description' => 'Calibrado y con funda protectora',
            'input_type' => ItemInputType::BOOLEAN,
            'is_mandatory' => true,
            'requires_photo_on_fail' => true,
            'order_index' => 3,
        ]);
    }
}
