<?php

namespace App\User\UI\Controller;

use App\User\Application\Command\DeleteUserCommand;
use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\Handler\DeleteUserHandler;
use App\User\Application\Handler\GetUsersHandler;
use App\User\Application\Handler\UpdateUserHandler;
use App\User\Application\ReadModel\UserView;
use App\User\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'user')]
final class UserController extends AbstractController
{
    public function __construct(
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(Request $request, GetUsersHandler $getUsersHandler): JsonResponse
    {
        $page = (int) $request->query->get('page', '1');
        $limit = (int) $request->query->get('limit', '10');

        $result = $getUsersHandler->__invoke($page, $limit);

        return new JsonResponse($result);
    }

    #[Route('/', name: 'delete', methods: ['DELETE'])]
    public function delete(DeleteUserHandler $deleteUserHandler): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
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
        $user = $this->getAuthenticatedUser();

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

        $user = $this->getAuthenticatedUser();

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

    /**
     * @return User
     */
    private function getAuthenticatedUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User is not authenticated or invalid user type.');
        }

        return $user;
    }
}
