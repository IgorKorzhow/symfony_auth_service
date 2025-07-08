<?php

namespace App\Tests\Controller\SecurityController;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class SecurityControllerLoginUserTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }

        $this->entityManager->close();
        parent::tearDown();
    }

    public function testSuccessLogin(): void
    {
        $user = UserFactory::new()->withoutPersisting()->create([
            'email' => 'test@gmail.com',
            'password' => 'test123',
        ]);

        $this->client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'],
            content: json_encode([
                'name' =>  $user->getName(),
                'email' => 'test@gmail.com',
                'password' => 'test123',
                'phone' => $user->getPhone(),
            ]),
        );

        self::assertResponseIsSuccessful();

        // TODO: Igor: Work in postman right by here got error in creds
        $this->client->request(
            method: 'POST',
            uri: '/api/login',
            server: ['CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'],
            content: json_encode([
                'email' => 'test@gmail.com',
                'password' => 'test123',
            ]),
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertNotEmpty($data['token']);
    }

    public function testLoginFailed(): void
    {
        $user = UserFactory::new()->withoutPersisting()->create();

        $this->client->request(
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
