<?php

namespace App\Tests\Unit;

use App\Security\JwtTokenService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JwtControllerTests extends WebTestCase
{
    private static ?object $service;

    public static function setUpBeforeClass()
    {
        //start the symfony kernel
        $kernel = static::createKernel();
        $kernel->boot();

        //get the DI container
        self::$container = $kernel->getContainer();

        //now we can instantiate our service (if you want a fresh one for
        //each test method, do this in setUp() instead
        self::$service = self::$container->get(JwtTokenService::class);
    }

    public function testJwtIssueTokenValidate(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/token/issue',
            [
                'email' => 'invalid',
                'password' => 'test123',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('This value is not a valid email address.', $response['response']['message']);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testJwtIssueToken(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/token/issue',
            [
                'email' => 'admin@example.com',
                'password' => 'test123',
                'app' => '822ea877-db3d-4f18-9453-1bca2da46f20',
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        $service = self::$service;

        $token = $service->parse($response['token']);

        dd($token);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
