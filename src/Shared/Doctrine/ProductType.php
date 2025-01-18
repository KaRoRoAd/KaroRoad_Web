<?php

declare(strict_types=1);

namespace App\Shared\Doctrine;

use App\Calculators\Entity\ProductTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class ProductType extends StringType
{
    public function getName(): string
    {
        return 'product_type';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return is_string($value) ? $value : $value->value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductTypeEnum
    {
        if ($value === null) {
            return null;
        }

        return ProductTypeEnum::tryFrom($value);
    }
}
