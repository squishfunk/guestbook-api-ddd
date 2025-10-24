<?php

namespace App\User\UI\Controller;

use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Application\ReadModel\UserView;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\ValueObject\Email;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/auth', name: 'auth')]
final class AuthController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private UserRepositoryInterface $userRepository,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, CreateUserHandler $createUserHandler): JsonResponse
    {
        $command = $this->serializer->deserialize($request->getContent(), CreateUserCommand::class, 'json');
        $userView = $createUserHandler->__invoke($command);

        return new JsonResponse([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $userView
        ], 201);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email and password are required'
            ], 400);
        }

        try {
            $user = $this->userRepository->findByEmail(new Email($data['email']));

            if (!$user || !$user->password()->verify($data['password'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $this->jwtManager->create($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => new UserView(
                    $user->id()->value(),
                    $user->name(),
                    $user->email()->value(),
                    $user->createdAt()->format('Y-m-d H:i:s'),
                    $user->updatedAt()->format('Y-m-d H:i:s')
                )
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Login failed'
            ], 401);
        }
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Symfony will handle the logout via the firewall
        return new JsonResponse([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
}


