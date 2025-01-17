<?php

declare(strict_types=1);

namespace App\Tests\Security\Factory;

use App\Security\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
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
        return User::class;
    }

    /**
     * Umożliwia łatwe tworzenie użytkownika z określonymi danymi.
     */
    public static function newUser(): self
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
            'name' => self::faker()->name(),
            'surname' => self::faker()->lastName(),
            'phoneNumber' => '+48123456789',
            'email' => self::faker()->unique()->safeEmail(),
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'enabled' => true,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(static function (User $user): void {
                // Możesz dodać dodatkowe akcje po instancjacji, np. ustawienie domyślnych ról
                if (empty($user->getRoles())) {
                    $user->setRoles(['ROLE_USER']); // Domyślna rola
                }
            });
    }
}
