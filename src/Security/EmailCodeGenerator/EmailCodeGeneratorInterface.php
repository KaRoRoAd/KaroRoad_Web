<?php

declare(strict_types=1);

namespace App\Security\EmailCodeGenerator;

interface EmailCodeGeneratorInterface
{
    public function generate(): string;
}
