<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTests extends WebTestCase
{
    public function testLoginUser(): void
    {
        $client = static::createClient();

        $router = self::getContainer()->get('router');


    }
}
