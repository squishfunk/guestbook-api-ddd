<?php

namespace App\Post\UI\Controller;

use App\Post\Application\Command\CreatePostCommand;
use App\Post\Application\Command\UpdatePostCommand;
use App\Post\Application\Handler\CreatePostHandler;
use App\Post\Application\Handler\GetPostsHandler;
use App\Post\Application\Handler\UpdatePostHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/posts', name: 'posts')]
final class PostController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
    ){}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreatePostHandler $createPostHandler): JsonResponse
    {

        $command = $this->serializer->deserialize($request->getContent(), CreatePostCommand::class, 'json');
        $postView = $createPostHandler->__invoke($command);

        return new JsonResponse($postView, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(string $id, Request $request, UpdatePostHandler $updatePostsHandler): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $command = new UpdatePostCommand(
            $id,
            $data['message'] ?? null,
        );

        $result = $updatePostsHandler->__invoke($command);

        return new JsonResponse($result, 200);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetPostsHandler $getPostsHandler): JsonResponse
    {
        $page = (int) $request->query->get('page', '1');
        $limit = (int) $request->query->get('limit', '10');

        $result = $getPostsHandler->__invoke($page, $limit);

        return new JsonResponse($result);
    }
}

