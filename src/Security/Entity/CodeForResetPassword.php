<?php

declare(strict_types=1);

namespace App\Security\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CodeForResetPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column]
    private ?bool $useCode = null;

    public function __construct(string $code, int $userId)
    {
        $this->code = $code;
        $this->userId = $userId;
        $this->useCode = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function isUseCode(): ?bool
    {
        return $this->useCode;
    }

    public function setUseCode(bool $useCode): static
    {
        $this->useCode = $useCode;

        return $this;
    }
}
