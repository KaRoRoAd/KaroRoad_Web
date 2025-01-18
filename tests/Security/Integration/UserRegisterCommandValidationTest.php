<?php

declare(strict_types=1);

namespace App\Tests\Security\Integration;

use App\Security\Validator\ValidationMessageEnum;
use App\Tests\ApiTestCase;
use JsonException;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserRegisterCommandValidationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    private const API_URI = '/api/users/register';
    private const CONTENT_TYPE = 'application/ld+json';
    public function testUserRegistration(): void
    {
        $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'email' => 'Test1@example.com',
                    'password' => 'Password123!',
                    'confirmPassword' => 'Password123!',
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
                    'password' => 'StrongPass123!',
                    'confirmPassword' => 'StrongPass123!',
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
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'email' => 'invalid-email',
                    'phoneNumber' => '+48123456789',
                    'password' => 'StrongPass123!',
                    'confirmPassword' => 'StrongPass123!',
                    'agree' => true,
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
    public function testValidationFailsForEmptyPassword(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'email' => 'test@example.com',
                    'phoneNumber' => '+48123456789',
                    'password' => '',
                    'confirmPassword' => 'StrongPass123!',
                    'agree' => true,
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolations = [
            [
                'propertyPath' => 'confirmPassword',
                'message' => ValidationMessageEnum::PASSWORDS_DO_NOT_MATCH->value,
                'code' => null,
            ],
            [
                'propertyPath' => 'password',
                'message' => ValidationMessageEnum::PASSWORD_REQUIRED->value,
                'code' => 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
            ],
        ];
        foreach ($expectedViolations as $expectedViolation) {
            $this->assertContainsEquals($expectedViolation, $response['violations']);
        }
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForNonMatchingPasswords(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'email' => 'test@example.com',
                    'phoneNumber' => '+48123456789',
                    'password' => 'StrongPass123!',
                    'confirmPassword' => 'DifferentPass123!',
                    'agree' => true,
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'confirmPassword',
            'message' => ValidationMessageEnum::PASSWORDS_DO_NOT_MATCH->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForShortPassword(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'email' => 'test@example.com',
                    'phoneNumber' => '+48123456789',
                    'password' => 'Short1!',
                    'confirmPassword' => 'Short1!',
                    'agree' => true,
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'password',
            'message' => ValidationMessageEnum::PASSWORD_STRENGTH_LENGTH->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForPasswordWithoutUppercase(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'email' => 'test@example.com',
                    'phoneNumber' => '+48123456789',
                    'password' => 'password1!',
                    'confirmPassword' => 'password1!',
                    'agree' => true,
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'password',
            'message' => ValidationMessageEnum::PASSWORD_STRENGTH_UPPERCASE->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForPasswordWithoutNumber(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'email' => 'test@example.com',
                    'phoneNumber' => '+48123456789',
                    'password' => 'Password!',
                    'confirmPassword' => 'Password!',
                    'agree' => true,
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'password',
            'message' => ValidationMessageEnum::PASSWORD_STRENGTH_NUMBER->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForPasswordWithoutSpecialCharacter(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'email' => 'test@example.com',
                    'phoneNumber' => '+48123456789',
                    'password' => 'Password1',
                    'confirmPassword' => 'Password1',
                    'agree' => true,
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'password',
            'message' => ValidationMessageEnum::PASSWORD_STRENGTH_SPECIAL_CHARACTER->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }
}
