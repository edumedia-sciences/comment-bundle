<?php

namespace eduMedia\CommentBundle\Entity;

trait CommentableTrait
{
    public function getCommentableType(): string
    {
        return self::class;
    }

    public function getCommentableId(): int
    {
        return $this->getId();
    }
}
