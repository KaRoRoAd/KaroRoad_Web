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
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserVerifyCommandValidationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    private const API_URI = '/api/users/verify';
    private const CONTENT_TYPE = 'application/ld+json';

    /**
     * @throws \JsonException
     */
    public function testUserVerificationSucceeds(): void
    {
        $user = UserFactory::newUser()->create();

        $tokenGenerator = self::getContainer()->get(EmailTokenGeneratorInterface::class);
        $token = $tokenGenerator->generate($user->getEmail(), $user->getId());

        $response = $this->browser()
            ->post(self::API_URI, [
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE,
                ],
                'json' => [
                    'token' => $token,
                    'email' => $user->getEmail(),
                ],
            ]);
        $response->assertStatus(Response::HTTP_ACCEPTED);

        $userRepository = self::getContainer()->get(UserQueryRepositoryInterface::class);
        $verifiedUser = $userRepository->findOneByEmail($user->getEmail());

        $this->assertTrue($verifiedUser->getEmailConfirmed());
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
                    'token' => '',
                    'email' => 'test@example.com',
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
                    'token' => 'fdasdasgdasgdsagdadgfdas',
                    'email' => 'test@example.com',
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
                    'token' => 'some-valid-token',
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
                    'token' => 'some-valid-token',
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
