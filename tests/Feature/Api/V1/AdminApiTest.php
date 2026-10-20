<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class AdminApiTest extends TestCase
{
    public function test_non_admin_cannot_access_admin_endpoints(): void
    {
        $operator = User::where('role', 'operator')->first();
        Sanctum::actingAs($operator);

        $response = $this->getJson('/api/v1/admin/daily-activity');
        $response->assertStatus(403);

        $response = $this->getJson('/api/v1/admin/operators');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_daily_activity(): void
    {
        $admin = User::where('role', 'admin')->first();
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/daily-activity');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'date',
                    'date_human',
                    'metrics' => [
                        'inspections_today',
                        'operators_active_today',
                        'total_operators',
                        'open_defects',
                        'critical_defects_today',
                        'fleet' => [
                            'total',
                            'active',
                            'maintenance',
                            'out_of_service',
                        ],
                    ],
                    'timeline',
                ],
            ]);
    }

    public function test_admin_can_list_and_create_operators(): void
    {
        $admin = User::where('role', 'admin')->first();
        Sanctum::actingAs($admin);

        // List operators
        $listResponse = $this->getJson('/api/v1/admin/operators');
        $listResponse->assertOk()
            ->assertJsonPath('success', true);

        // Create new operator
        $email = 'operador.test.' . uniqid() . '@fleettech.pe';
        $createResponse = $this->postJson('/api/v1/admin/operators', [
            'name' => 'Operador Test Nuevo',
            'email' => $email,
            'password' => 'password123',
            'phone' => '+51 988777666',
            'is_active' => true,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Operador Test Nuevo')
            ->assertJsonPath('data.role', 'operator');
    }
}
