<?php

namespace App\Tests\Controller\SecurityController;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class SecurityControllerLoginUserTest extends WebTestCase
{
    use Factories;

    public function testSuccessLogin(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->withoutPersisting()->create([
            'email' => 'test@gmail.com',
            'password' => 'test123',
        ]);

        $client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'],
            content: json_encode([
                'name' => $user->getName(),
                'email' => 'test@gmail.com',
                'password' => 'test123',
                'phone' => $user->getPhone(),
            ]),
        );

        self::assertResponseIsSuccessful();

        $client->request(
            method: 'POST',
            uri: '/api/login',
            server: ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'],
            content: json_encode([
                'email' => 'test@gmail.com',
                'password' => 'test123',
            ]),
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertNotEmpty($data['token']);
    }

    public function testLoginFailed(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->withoutPersisting()->create();

        $client->request(
            method: 'POST',
            uri: '/api/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
            ]),
        );

        self::assertResponseStatusCodeSame(401);
    }
}
