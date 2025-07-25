<?php

namespace App\Tests\Controller\SecurityController;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class SecurityControllerRegisterUserTest extends WebTestCase
{
    use Factories;

    public function testSuccessRegister(): void
    {
        $client = static::createClient();

        $client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' => 'test name',
                'email' => 'test@gmail.com',
                'password' => 'testpassword',
                'phone' => '123321',
            ]),
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertSame('Success', $data['status']);

        /** @var $userRepository UserRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'test@gmail.com']);

        self::assertNotEmpty($user);
    }

    public static function validationErrorDataProvider(): array
    {
        return [
            [
                'requestData' => [
                    'email' => '',
                ],
            ],
            // TODO Igor: add testcases for check validation
        ];

    }

    #[DataProvider('validationErrorDataProvider')]
    public function testValidationError(array $requestData): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->withoutPersisting()->create($requestData);

        $client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'phone' => $user->getPhone(),
            ]),
        );

        self::assertResponseStatusCodeSame(422);
    }

    public function testUniqueEmailConstraint(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->create();

        $client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'phone' => $user->getPhone(),
            ]),
        );

        self::assertResponseStatusCodeSame(422);
    }
}
