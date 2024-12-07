<?php

declare(strict_types=1);

namespace App\Shared\Event;

final readonly class UserRegisterEvent
{
    public function __construct(
        public int $userId,
        public string $email
    ) {
    }
}