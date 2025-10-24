<?php

namespace App\User\UI\Controller;

use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Command\DeleteUserCommand;
use App\User\Application\Command\GetUserCommand;
use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Application\Handler\DeleteUserHandler;
use App\User\Application\Handler\GetUserHandler;
use App\User\Application\Handler\GetUsersHandler;
use App\User\Application\Handler\UpdateUserHandler;
use App\User\Application\ReadModel\UserView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user', name: 'user')]
final class UserController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(Request $request, GetUsersHandler $getUsersHandler): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $result = $getUsersHandler->__invoke($page, $limit);

        return new JsonResponse($result);
    }

    #[Route('/', name: 'delete', methods: ['DELETE'])]
    public function delete(DeleteUserHandler $deleteUserHandler): JsonResponse
    {
        $user = $this->getUser();
        $command = new DeleteUserCommand($user->id()->value());
        $deleteUserHandler->__invoke($command);

        return new JsonResponse([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        return new JsonResponse([
            'success' => true,
            'user' => new UserView(
                $user->id()->value(),
                $user->name(),
                $user->email()->value(),
                $user->createdAt()->format('Y-m-d H:i:s'),
                $user->updatedAt()->format('Y-m-d H:i:s')
            )
        ]);
    }

    #[Route('/me', name: 'update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, UpdateUserHandler $updateUserHandler): JsonResponse
    {

        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $data = json_decode($request->getContent(), true);
        $command = new UpdateUserCommand(
            $user->id()->value(),
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['password'] ?? null
        );

        $userView = $updateUserHandler->__invoke($command);

        return new JsonResponse([
            'success' => true,
            'user' => $userView
        ]);
    }
}
