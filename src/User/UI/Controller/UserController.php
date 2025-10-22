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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user', name: 'user')]
final class UserController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateUserHandler $createUserHandler): JsonResponse
    {
        if ('json' !== $request->getContentTypeFormat()) {
            throw new BadRequestException('Unsupported content format');
        }

        $command = $this->serializer->deserialize($request->getContent(), CreateUserCommand::class, 'json');
        $userView = $createUserHandler->__invoke($command);

        return new JsonResponse([
            'success' => true,
            'user' => $userView
        ], 201);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetUserHandler $getUserHandler): JsonResponse
    {
        $command = new GetUserCommand($id);
        $userView = $getUserHandler->__invoke($command);

        return new JsonResponse([
            'success' => true,
            'user' => $userView
        ]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetUsersHandler $getUsersHandler): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $result = $getUsersHandler->__invoke($page, $limit);

        return new JsonResponse($result);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request, UpdateUserHandler $updateUserHandler): JsonResponse
    {
        if ('json' !== $request->getContentTypeFormat()) {
            throw new BadRequestException('Unsupported content format');
        }

        $data = json_decode($request->getContent(), true);
        $command = new UpdateUserCommand(
            $id,
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

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteUserHandler $deleteUserHandler): JsonResponse
    {
        $command = new DeleteUserCommand($id);
        $deleteUserHandler->__invoke($command);

        return new JsonResponse([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
