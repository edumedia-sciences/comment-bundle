<?php

namespace eduMedia\CommentBundle\Entity;

interface CommentableInterface
{
    public function getCommentableType(): string;

    public function getCommentableId(): int;
}
