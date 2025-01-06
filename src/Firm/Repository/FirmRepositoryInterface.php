<?php

declare(strict_types=1);

namespace App\Firm\Repository;

use App\Firm\Entity\Firm;

interface FirmRepositoryInterface
{
    public function save(Firm $firm): Firm;
}