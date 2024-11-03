<?php

namespace App\Controller;

use App\Dto\Auth\LoginDto;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(#[MapRequestPayload] LoginDto $loginDto, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        $user = $this->userRepository->checkLoginCredentials($loginDto);

        if (!$user) {
            throw new UnauthorizedHttpException('login', 'Invalid credentials');
        }

        return $this->json([
            'token' => $JWTTokenManager->create($user),
        ]);
    }
}
