<?php

namespace App\Tests\Controller\SecurityController;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class SecurityControllerRegisterUserTest extends WebTestCase
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

    public function testSuccessRegister(): void
    {
        $this->client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' =>  'test name',
                'email' => 'test@gmail.com',
                'password' => 'testpassword',
                'phone' => '123321',
            ]),
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

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
                'responseData' => [
                    'message' => 'Validation failed',
                    'errors' => [
                        'email' => [
                            'This value should not be blank.'
                        ],
                    ],
                ],
            ],
            // TODO Igor: add testcases for check validation
        ];

    }

    #[DataProvider('validationErrorDataProvider')]
    public function testValidationError(array $requestData, array $responseData): void
    {
        $user = UserFactory::new()->withoutPersisting()->create($requestData);

        $this->client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' =>  $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'phone' => $user->getPhone(),
            ]),
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(422);
        self::assertSame($data, $responseData);
    }

    public function testUniqueEmailConstraint(): void
    {
        $user = UserFactory::new()->create();

        $this->client->request(
            method: 'POST',
            uri: '/api/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' =>  $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'phone' => $user->getPhone(),
            ]),
        );

        self::assertResponseStatusCodeSame(422);
    }
}
