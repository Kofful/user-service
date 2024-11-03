<?php

namespace App\Controller;

use App\Dto\User\GetUsersDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', 'users_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Security $security,
    ) {
    }

    #[Route('', name: 'get_all', methods: 'GET')]
    public function getAll(#[MapQueryString] ?GetUsersDto $getUsersDto): JsonResponse
    {
        // if the user is not admin, they shouldn't have access to other logins
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();
            $requestedLogin = $getUsersDto?->login;

            if ($requestedLogin && $requestedLogin !== $currentUser->getLogin()) {
                throw new AccessDeniedHttpException("You don't have permission to this login");
            }

            // add filtering by current user's login
            $getUsersDto = new GetUsersDto($currentUser->getLogin());
        }

        $user = $this->userRepository->getUsers($getUsersDto);

        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_user'],
        );
    }
}
