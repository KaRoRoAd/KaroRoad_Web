<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\EmailTokenGenerator;

use App\Security\EmailTokenGenerator\EmailTokenGenerator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTokenGeneratorTest extends TestCase
{
    private const EMAIL = 'email@mail.com';
    private const USER_ID = 1;
    private const SECRET = 'secret';

    private EmailTokenGenerator $emailTokenGenerator;

    protected function setUp(): void
    {
        $this->emailTokenGenerator = new EmailTokenGenerator(self::SECRET);
    }

    public function testGenerateToken(): void
    {
        $token = $this->emailTokenGenerator->generate(self::EMAIL, self::USER_ID);

        $this->assertNotEmpty($token, 'Token should not be empty');
        $this->assertStringContainsString('.', $token, 'Token should contain a dot separator');

        $parts = explode('.', $token);
        $this->assertCount(2, $parts, 'Token should have two parts separated by a dot');

        $this->assertValidBase64Url($parts[0], 'First part should be valid base64');
        $this->assertValidBase64Url($parts[1], 'Second part should be valid base64');
    }

    public function testDecodeToken(): void
    {
        $token = $this->emailTokenGenerator->generate(self::EMAIL, self::USER_ID);
        $decodedData = $this->emailTokenGenerator->decode($token);

        $this->assertIsArray($decodedData, 'Decoded data should be an array');
        $this->assertArrayHasKey('email', $decodedData, 'Decoded data should contain email');
        $this->assertArrayHasKey('userId', $decodedData, 'Decoded data should contain userId');
        $this->assertArrayHasKey('expired', $decodedData, 'Decoded data should contain expired');
        $this->assertEquals(self::EMAIL, $decodedData['email'], 'Email should match');
        $this->assertEquals(self::USER_ID, $decodedData['userId'], 'User ID should match');

        $this->assertGreaterThan(time(), $decodedData['expired'], 'Token should not be expired');
    }

    public function testDecodeInvalidTokenFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->emailTokenGenerator->decode('invalidtoken');
    }

    public function testDecodeInvalidTokenSignature(): void
    {
        $validToken = $this->emailTokenGenerator->generate(self::EMAIL, self::USER_ID);
        $parts = explode('.', $validToken);
        $invalidToken = $parts[0] . '.invalidsignature';

        $this->expectException(InvalidArgumentException::class);
        $this->emailTokenGenerator->decode($invalidToken);
    }

    public function testDecodeInvalidBase64Token(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->emailTokenGenerator->decode('invalid!@#.signature');
    }

    public function testDecodeExpiredToken(): void
    {
        $expiredToken = $this->emailTokenGenerator->generate(self::EMAIL, self::USER_ID);
        $decodedData = $this->emailTokenGenerator->decode($expiredToken);
        $decodedData['expired'] = time() - 1;

        $this->expectException(InvalidArgumentException::class);
        $this->emailTokenGenerator->decode(json_encode($decodedData));
    }

    public function testGenerateTokenWithLongEmail(): void
    {
        $longEmail = str_repeat('a', 244) . '@mail.com';
        $token = $this->emailTokenGenerator->generate($longEmail, self::USER_ID);

        $this->assertNotEmpty($token, 'Token should not be empty even with long email');
    }

    public function testGenerateTokenWithHighUserId(): void
    {
        $highUserId = PHP_INT_MAX;
        $token = $this->emailTokenGenerator->generate(self::EMAIL, $highUserId);

        $decodedData = $this->emailTokenGenerator->decode($token);
        $this->assertEquals($highUserId, $decodedData['userId'], 'User ID should match the high value');
    }

    private function assertValidBase64Url(string $data, string $message = ''): void
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        $this->assertNotFalse($decoded, $message);
    }
}
