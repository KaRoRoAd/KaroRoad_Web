<?php

declare(strict_types=1);

namespace App\Tests\Security\Integration;

use App\Security\EmailTokenGenerator\EmailTokenGeneratorInterface;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Validator\ValidationMessageEnum;
use App\Tests\ApiTestCase;
use App\Tests\Security\Factory\UserFactory;
use JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserResetPasswordCommandValidationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    private const API_URI = '/api/users/reset-password';
    private const CONTENT_TYPE = 'application/ld+json';

    /**
     * @throws JsonException
     */
    public function testUserResetPasswordSucceeds(): void
    {
        $user = UserFactory::newUser()->create();
        $tokenGenerator = self::getContainer()->get(EmailTokenGeneratorInterface::class);
        $token = $tokenGenerator->generate($user->getEmail(), $user->getId());
        $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'token' => $token,
                    'email' => $user->getEmail(),
                    'password' => 'NewValidPassword123!',
                    'confirmPassword' => 'NewValidPassword123!',
                ],
            ])->assertStatus(Response::HTTP_ACCEPTED);
        $userRepository = self::getContainer()->get(UserQueryRepositoryInterface::class);
        $updatedUser = $userRepository->findOneByEmail($user->getEmail());
        $this->assertNotNull($updatedUser);
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($passwordHasher->isPasswordValid($updatedUser, 'NewValidPassword123!'));
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
                    'password' => 'ValidPassword123!',
                    'confirmPassword' => 'ValidPassword123!',
                    'token' => 'some-valid-token',
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
                    'password' => 'ValidPassword123!',
                    'confirmPassword' => 'ValidPassword123!',
                    'token' => 'some-valid-token',
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
                    'email' => 'test@example.com',
                    'password' => '',
                    'confirmPassword' => 'ValidPassword123!',
                    'token' => 'some-valid-token',
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'password',
            'message' => ValidationMessageEnum::PASSWORD_REQUIRED->value,
            'code' => $response['violations'][1]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
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
                    'email' => 'test@example.com',
                    'password' => 'ValidPassword123!',
                    'confirmPassword' => 'DifferentPassword123!',
                    'token' => 'some-valid-token',
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
    public function testValidationFailsForEmptyToken(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'ValidPassword123!',
                    'confirmPassword' => 'ValidPassword123!',
                    'token' => '',
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'token',
            'message' => ValidationMessageEnum::TOKEN_REQUIRED->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    /**
     * @throws JsonException
     */
    public function testValidationFailsForInvalidToken(): void
    {
        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'ValidPassword123!',
                    'confirmPassword' => 'ValidPassword123!',
                    'token' => 'fasadvsagdvsafaefdsfds',
                ],
            ])
            ->assertStatus(422)
            ->json()
            ->decoded();

        $this->assertArrayHasKey('violations', $response);

        $expectedViolation = [
            'propertyPath' => 'token',
            'message' => ValidationMessageEnum::INVALID_TOKEN_FORMAT_OR_SIGNATURE->value,
            'code' => $response['violations'][0]['code'],
        ];

        $this->assertContainsEquals($expectedViolation, $response['violations']);
    }

    public function testValidationFailsForMissingParameters(): void
    {
        $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [],
            ])
            ->assertStatus(400);
    }
}
