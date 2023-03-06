<?php

namespace eduMedia\CommentBundle\Entity;

use DateTimeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CommentInterface
{
    public function getId();

    public function getContent(): ?string;

    public function setContent(string $content): self;

    public function getCreatedAt(): ?DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): self;

    public function getResourceType(): ?string;

    public function setResourceType(string $type): self;

    public function getResourceId(): ?int;

    public function setResourceId(int $id): self;

    public function setResource(CommentableInterface $resource): self;

    public function getAuthor(): ?UserInterface;

    public function setAuthor(UserInterface $author): self;
}
