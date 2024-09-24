<?php

namespace eduMedia\CommentBundle\EventListener;

use Doctrine\ORM\Event\PreRemoveEventArgs;
use eduMedia\CommentBundle\Entity\CommentableInterface;
use eduMedia\CommentBundle\Service\CommentService;

readonly class CommentListener
{
    public function __construct(private CommentService $commentService)
    {
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        if (($resource = $args->getObject()) and $resource instanceof CommentableInterface) {
            $this->commentService->deleteAllComments($resource);
        }
    }
}
