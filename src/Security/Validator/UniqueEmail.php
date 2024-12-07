<?php

declare(strict_types=1);

namespace App\Security\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class UniqueEmail extends Constraint
{
    public string $message;

    public function __construct(
        string $message = ValidationMessageEnum::EMAIL_ALREADY_REGISTERED->value,
        array $options = []
    ) {
        parent::__construct($options);
        $this->message = $message;
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
