<?php
namespace Wiakowe\FetchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class FetchMailCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mail:fetch')
            ->setDescription('Fetches and processes all the matching mails.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting the mail fetching<info>');

        $this->getContainer()->get('wiakowe_fetch.processing.planner')
            ->processMails($this->getContainer()->get('wiakowe_fetch.client'));
    }
}
