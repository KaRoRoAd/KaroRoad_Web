<?php

declare(strict_types=1);

namespace App\Shared\Owner;

use App\Shared\Owner\Dto\OwnerDto;

interface OwnerProviderInterface
{
    public function getOwnerDto(): ?OwnerDto;

    public function getToken(): ?string;
}
