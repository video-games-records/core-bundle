<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Repository\VideoCommentRepository;
use VideoGamesRecords\CoreBundle\Filter\Bbcode as BbcodeFilter;

class CommentCommand extends Command
{
    protected static $defaultName = 'vgr-video:comment';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('vgr-video:comment')
            ->setDescription('Command for a comment article')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
            );
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool|int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $function = $input->getArgument('function');

        switch ($function) {
            case 'migrate':
                $this->migrate();
                break;
        }

        return 0;
    }


    /**
     *
     */
    private function migrate()
    {
        /** @var VideoCommentRepository $commentRepository */
        $commentRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:VideoComment');

        $bbcodeFiler = new BbcodeFilter();
        $comments = $commentRepository->findAll();
        foreach ($comments as $comment) {
            $comment->setText($bbcodeFiler->filter($comment->getText()));
        }
        $this->em->flush();
    }
}
