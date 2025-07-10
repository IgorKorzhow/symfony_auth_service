<?php

namespace App\Controller;

use App\Dto\RegisterUserDto;
use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Exceptions\DtoValidationException;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SecurityController extends AbstractController
{
    /**
     * @throws DtoValidationException
     */
    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function register(
        ValidatorInterface $validator,
        RegisterUserDto $dto,
        UserRepository $userRepository,
        EventDispatcherInterface $dispatcher,
    ): JsonResponse {
        $dto->validate($validator);

        $user = $userRepository->register($dto);

        $dispatcher->dispatch(new UserRegisteredEvent($user));

        return $this->json(['status' => 'Success'], Response::HTTP_CREATED);
    }

    #[Route('/api/logout', name: 'logout', methods: ['POST'])]
    public function logout(Request $request, EventDispatcherInterface $eventDispatcher, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $eventDispatcher->dispatch(new LogoutEvent($request, $tokenStorage->getToken()));

        return new JsonResponse();
    }

    #[Route('/api/user', name: 'user', methods: ['GET'])]
    public function getAuthenticatedUser(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'name' => $user->getName(),
            'phone' => $user->getPhone(),
        ]);
    }
}
