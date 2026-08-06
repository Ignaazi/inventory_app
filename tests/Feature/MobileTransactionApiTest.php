<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class MobileTransactionApiTest extends TestCase
{
    public function test_transaction_endpoints_require_a_bearer_token(): void
    {
        $endpoints = [
            '/api/engineering/transactions/in',
            '/api/engineering/transactions/out',
            '/api/engineering/transactions/return',
            '/api/engineering/transactions/disposal',
            '/api/production/transactions/in',
            '/api/production/transactions/out',
            '/api/production/transactions/return',
        ];

        foreach ($endpoints as $endpoint) {
            $this->post($endpoint, ['barcode_scan' => 'TEST'])
                ->assertUnauthorized();
        }

        foreach ([
            '/api/engineering/transactions/history',
            '/api/production/transactions/history',
        ] as $endpoint) {
            $this->get($endpoint)->assertUnauthorized();
        }
    }

    public function test_costing_users_cannot_call_transaction_endpoints(): void
    {
        $user = new User(['name' => 'Costing', 'role' => 'costing']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/engineering/transactions/out', ['barcode_scan' => 'TEST'])
            ->assertForbidden();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/production/transactions/out', ['barcode_scan' => 'TEST'])
            ->assertForbidden();
    }
}
