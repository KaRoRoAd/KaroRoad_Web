<?php

declare(strict_types=1);

namespace App\Shared\Event;

final readonly class ResetUserPasswordEvent
{
    public function __construct(
        public string $email,
        public string $token,
    ) {
    }
}
