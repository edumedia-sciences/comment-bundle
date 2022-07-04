<?php

namespace eduMedia\CommentBundle\Command;

use eduMedia\CommentBundle\Service\CommentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'edumedia:comment:delete-all',
    description: 'Delete all resource comments',
)]
class DeleteAllCommand extends Command
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

        $this->commentService->deleteAllComments($resource);

        $io->success("Delete all comments for {$resource->getCommentableType()} {$resource->getCommentableId()}");

        return Command::SUCCESS;
    }
}
