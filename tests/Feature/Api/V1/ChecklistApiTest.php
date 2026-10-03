<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\ChecklistTemplate;
use App\Models\User;
use Tests\TestCase;

final class ChecklistApiTest extends TestCase
{
    public function test_can_list_active_checklist_templates(): void
    {
        $user = User::where('email', 'operador.manana@fleettech.pe')->firstOrFail();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/checklist-templates');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_fetch_full_template_with_sections_and_tools(): void
    {
        $user = User::where('email', 'operador.manana@fleettech.pe')->firstOrFail();
        $template = ChecklistTemplate::firstOrFail();

        $response = $this->actingAs($user)
            ->getJson("/api/v1/checklist-templates/{$template->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'sections' => [
                        '*' => [
                            'id',
                            'title',
                            'items' => [
                                '*' => [
                                    'id',
                                    'label',
                                    'input_type',
                                    'requires_photo_on_fail',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
