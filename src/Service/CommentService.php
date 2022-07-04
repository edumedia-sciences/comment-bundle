<?php

namespace eduMedia\CommentBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use eduMedia\CommentBundle\Entity\CommentableInterface;
use eduMedia\CommentBundle\Entity\CommentInterface;
use Symfony\Component\Security\Core\Security;

class CommentService
{
    private Collection $entityComments;

    public function __construct(
        private EntityManagerInterface $manager,
        private Security $security,
        private string $commentClass = 'App\Entity\Comment',
    ) {
        $this->entityComments = new ArrayCollection();
    }

    public function createComment(string $content): CommentInterface
    {
        return new $this->commentClass($content);
    }

    public function getComments(CommentableInterface $resource, bool $autoload = false): Collection
    {
        if ($autoload) {
            $this->loadComments($resource);
        }

        $key = $this->getResourceKey($resource);

        if (!$this->entityComments->containsKey($key)) {
            $this->entityComments->set($key, new ArrayCollection());
        }

        return $this->entityComments->get($key);
    }

    public function addComment(CommentInterface $comment, CommentableInterface $resource, bool $andFlush = false, bool $isReplaceMode = false): self
    {
        $comments = $this->getComments($resource);

        if ($comments->contains($comment)) {
            return $this;
        }

        $comment->setResource($resource);

        if (!$isReplaceMode
                && $comment->getAuthor() === null
                && $this->security->getUser() !== null) {
            $comment->setAuthor($this->security->getUser());
        }

        $comments->add($comment);

        $this->manager->persist($comment);

        if ($andFlush) {
            $this->manager->flush();
        }

        return $this;
    }

    /**
     * @param CommentInterface[] $comments
     */
    public function addComments(array $comments, CommentableInterface $resource, bool $andFlush = false, bool $isReplaceMode = false): self
    {
        foreach ($comments as $comment) {
            if ($comment instanceof CommentInterface) {
                $this->addComment($comment, $resource, isReplaceMode: $isReplaceMode);
            }
        }

        if ($andFlush) {
            $this->manager->flush();
        }

        return $this;
    }

    public function deleteComment(CommentInterface $comment, CommentableInterface $resource, bool $andFlush = false): self
    {
        $this->getComments($resource)->removeElement($comment);
        $this->manager->remove($comment);

        if ($andFlush) {
            $this->manager->flush();
        }

        return $this;
    }

    public function deleteAllComments(CommentableInterface $resource): self
    {
        $this->manager
            ->createQueryBuilder()
            ->delete($this->commentClass, 'c')
            ->andWhere('c.resourceType = :type')
            ->andWhere('c.resourceId = :id')
            ->setParameter('type', $resource->getCommentableType())
            ->setParameter('id', $resource->getCommentableId())
            ->getQuery()
            ->getResult();

        return $this->replaceComments([], $resource);
    }

    /**
     * @param CommentInterface[] $comments
     */
    private function replaceComments(array $comments, CommentableInterface $resource): self
    {
        $this->entityComments->remove($this->getResourceKey($resource));

        return $this->addComments($comments, $resource, isReplaceMode: true);
    }

    private function loadComments(CommentableInterface $resource): self
    {
        $comments = $this->queryComments($resource);

        return $this->replaceComments($comments, $resource);
    }

    /* --- */

    private function getResourceKey(CommentableInterface $resource): string
    {
        return $resource->getCommentableType() . ':' . $resource->getCommentableId();
    }

    /**
     * @return CommentInterface[]
     */
    protected function queryComments(CommentableInterface $resource): array
    {
        return $this->manager
            ->createQueryBuilder()
            ->select('c')
            ->from($this->commentClass, 'c')
            ->andWhere('c.resourceType = :type')
            ->andWhere('c.resourceId = :id')
            ->setParameter('type', $resource->getCommentableType())
            ->setParameter('id', $resource->getCommentableId())
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* --- */

    public function getResource(string $type, int $id): ?CommentableInterface
    {
        $resource = $this->manager->getRepository($type)->find($id);

        if (is_null($resource)) {
            return null;
        }

        if (!($resource instanceof CommentableInterface)) {
            return null;
        }

        return $resource;
    }
}
