<?php

namespace App\Tests\Panther;

use Symfony\Component\Panther\PantherTestCase;

class DefaultTest extends PantherTestCase
{
    public function testHomePage(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/auth/' . '/login');
        $client->waitFor('[data-test="submit"]');
    }
}
