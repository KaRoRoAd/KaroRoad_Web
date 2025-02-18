<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;

abstract class ApiTestCase extends KernelTestCase
{
    use HasBrowser {
        browser as baseKernelBrowser;
    }

    protected function browser(array $options = [], array $server = [])
    {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/ld+json')
            )
        ;
    }

    protected function getUserToken(string $username, string $password): string
    {
        $json = $this->browser()
            ->post('/api/login/check', [
                'json' => ['username' => $username, 'password' => $password],
            ])
            ->assertJson()
            ->assertStatus(200)
            ->json();

        return $json->decoded()['token'];
    }
}
