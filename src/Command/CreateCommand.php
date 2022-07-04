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
    name: 'edumedia:comment:create',
    description: 'Create comment',
)]
class CreateCommand extends Command
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
            ->addArgument('resourceId', InputArgument::REQUIRED, 'Resource ID')
            ->addArgument('content', InputArgument::REQUIRED, 'Content');
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

        $comment = $this->commentService->createComment($input->getArgument('content'));
        $this->commentService->addComment($comment, $resource, true);
        $io->success('Created comment, ID = ' . $comment->getId());

        return Command::SUCCESS;
    }
}
