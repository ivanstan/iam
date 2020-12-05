<?php

namespace App\Tests\Unit;

use App\Security\JwtTokenService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JwtControllerTests extends WebTestCase
{
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
            ]
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        $service = new JwtTokenService(__DIR__ . '/../..');

        $service->parse($response['token']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
