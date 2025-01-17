<?php

declare(strict_types=1);

namespace App\Tests\Security\Integration;

use App\Security\Validator\ValidationMessageEnum;
use App\Tests\ApiTestCase;
use App\Tests\Security\Factory\UserFactory;
use JsonException;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserChangePasswordCommandValidationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    private const API_URI = '/api/users/change-password';
    private const CONTENT_TYPE = 'application/ld+json';

    /**
     * @throws JsonException
     */
    public function testChangePasswordSucceeds(): void
    {
        $user = UserFactory::newUser()->create();

        $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'email' => $user->getEmail(),
                ],
            ])
            ->assertStatus(202);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForEmptyEmail(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'email' => '',
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'email',
            'message' => ValidationMessageEnum::EMAIL_REQUIRED->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForInvalidEmail(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'email' => 'invalid-email',
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'email',
            'message' => ValidationMessageEnum::EMAIL_INVALID->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForNonExistentEmail(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'email' => 'nonexistent@example.com',
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'email.email',
            'message' => ValidationMessageEnum::EMAIL_NOT_EXIST->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForMissingParameters(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [],
            ])
            ->assertStatus(400);
    }
}
