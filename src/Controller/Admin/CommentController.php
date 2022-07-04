<?php

namespace eduMedia\CommentBundle\Controller\Admin;

use eduMedia\CommentBundle\Service\CommentService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    public function __construct(
        private CommentService $commentService
    ) {
    }

    #[Template('@eduMediaComment/admin/comments/list.html.twig')]
    public function list(string $resourceType, int $resourceId): array
    {
        $resource = $this->commentService->getResource($resourceType, $resourceId);
        if (is_null($resource)) {
            throw $this->createNotFoundException('Resource is not commentable');
        }

        return ['comments' => array_reverse($this->commentService->getComments($resource, true)->toArray())];
    }

    #[Template('@eduMediaComment/admin/comments/list.html.twig')]
    public function create(string $resourceType, int $resourceId, Request $request): array
    {
        $resource = $this->commentService->getResource($resourceType, $resourceId);
        if (is_null($resource)) {
            throw $this->createNotFoundException('Resource is not commentable');
        }

        $comment = $this->commentService->createComment($request->request->get('content'));

        $this->commentService->getComments($resource, true);
        $this->commentService->addComment($comment, $resource, true);

        return ['comments' => array_reverse($this->commentService->getComments($resource)->toArray())];
    }
}
