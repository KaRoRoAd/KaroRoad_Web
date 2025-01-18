<?php

declare(strict_types=1);

namespace App\Mailing\Sender;

interface MailingInterface
{
    public function send(string $to, array $data = []): void;
}
