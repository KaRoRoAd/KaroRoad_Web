<?php

declare(strict_types=1);

namespace App\Tests\Security\Integration;

use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserLoginResourceTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testCanLoginAsGuest(): void
    {
        $this->browser()
            ->post('/api/login/check', [
                'json' => ['username' => 'admin', 'password' => 'EZjxX3BayZSC'],
            ])
            ->assertJson()
            ->assertStatus(200)
            ->json()
            ->assertHas('token');
    }
}
