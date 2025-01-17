<?php

declare(strict_types=1);

namespace App\Tests\Security\Factory;

use App\Security\Entity\Firm;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Firm>
 */
final class FirmFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct(); // Nie zapomnij o wywołaniu konstruktora rodzica.
    }

    public static function class(): string
    {
        return Firm::class;
    }

    /**
     * Umożliwia łatwe tworzenie użytkownika z określonymi danymi.
     */
    public static function newFirm(): self
    {
        return self::new();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'uuid' => self::faker()->uuid(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (Firm $firm): void {
            });
    }
}