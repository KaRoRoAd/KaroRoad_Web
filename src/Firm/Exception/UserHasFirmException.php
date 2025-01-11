<?php

declare(strict_types=1);

namespace App\Firm\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class UserHasFirmException extends HttpException
{
    public function __construct()
    {
        parent::__construct(422, ExceptionEnum::USER_HAS_FIRM->value);
    }
}
