<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Tests\TestCase;

final class AuthApiTest extends TestCase
{
    public function test_operator_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'operador.manana@fleettech.pe',
            'password' => 'password123',
            'device_name' => 'Tablet-Patio-01',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'role_label',
                        'company' => ['id', 'name'],
                    ],
                    'token',
                ],
                'meta' => ['timestamp'],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'operador.manana@fleettech.pe',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_operator_can_fetch_profile(): void
    {
        $user = User::where('email', 'operador.manana@fleettech.pe')->firstOrFail();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'operador.manana@fleettech.pe');
    }
}
