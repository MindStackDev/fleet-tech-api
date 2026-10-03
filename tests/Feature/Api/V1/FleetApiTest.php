<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Models\Vehicle;
use Tests\TestCase;

final class FleetApiTest extends TestCase
{
    public function test_can_list_yard_vehicles(): void
    {
        $user = User::where('email', 'operador.manana@fleettech.pe')->firstOrFail();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/vehicles');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'plate_number',
                        'brand',
                        'model',
                        'status',
                        'specs',
                    ],
                ],
                'meta',
            ]);
    }

    public function test_can_fetch_vehicle_detail_with_specs(): void
    {
        $user = User::where('email', 'operador.manana@fleettech.pe')->firstOrFail();
        $vehicle = Vehicle::where('plate_number', 'ABC-101')->firstOrFail();

        $response = $this->actingAs($user)
            ->getJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.plate_number', 'ABC-101')
            ->assertJsonPath('data.brand', 'Volvo');
    }
}
