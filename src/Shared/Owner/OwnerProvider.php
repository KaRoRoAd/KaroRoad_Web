<?php

declare(strict_types=1);

namespace App\Shared\Owner;

use App\Shared\Owner\Dto\OwnerDto;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final readonly class OwnerProvider implements OwnerProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage
    ) {
    }

    public function getOwnerDto(): ?OwnerDto
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user) {
            return null;
        }

        return new OwnerDto($user->getId());
    }

    public function getToken(): ?string
    {
        return $this->tokenStorage->getToken()?->getCredentials();
    }
}
