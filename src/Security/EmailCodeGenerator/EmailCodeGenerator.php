<?php

declare(strict_types=1);

namespace App\Security\EmailCodeGenerator;

final readonly class EmailCodeGenerator implements EmailCodeGeneratorInterface
{
    private const CODE_LENGTH = 6;

    public function generate(): string
    {
        return str_pad((string) mt_rand(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }
}
