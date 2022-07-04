<?php

namespace eduMedia\CommentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use eduMedia\CommentBundle\Entity\CommentableInterface;
use eduMedia\CommentBundle\Service\CommentService;

class CommentListener
{
    public function __construct(private CommentService $commentService)
    {
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        if (($resource = $args->getEntity()) and $resource instanceof CommentableInterface) {
            $this->commentService->deleteAllComments($resource);
        }
    }
}
