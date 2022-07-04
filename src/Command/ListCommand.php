<?php

namespace eduMedia\CommentBundle\Command;

use eduMedia\CommentBundle\Entity\CommentInterface;
use eduMedia\CommentBundle\Service\CommentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'edumedia:comment:list',
    description: 'List resource comments',
)]
class ListCommand extends Command
{
    public function __construct(
        private CommentService $commentService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('resourceType', InputArgument::REQUIRED, 'Resource type')
            ->addArgument('resourceId', InputArgument::REQUIRED, 'Resource ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $resource = $this->commentService->getResource(
            $input->getArgument('resourceType'),
            $input->getArgument('resourceId')
        );

        if (is_null($resource)) {
            $io->error('Could not find requested resource (or it is not commentable)');

            return Command::FAILURE;
        }

        $comments = $this->commentService->getComments($resource, true);

        $io->info("Comments on {$resource->getCommentableType()} {$resource->getCommentableId()}");

        $io->table(['ID', 'Created', 'Author', 'Content'],
            array_map(
                fn(CommentInterface $comment) => [
                    $comment->getId(),
                    $comment->getCreatedAt()->format('d/m/Y H:i:s'),
                    $comment->getAuthor()?->getUserIdentifier(),
                    $comment->getContent(),
                ],
                $comments->toArray()
            ));

        return Command::SUCCESS;
    }
}
