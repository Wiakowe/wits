<?php
namespace Wiakowe\FetchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wiakowe\FetchBundle\Event\FetchMailEvents;

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

        /** @var $eventDispatcher \Symfony\Component\EventDispatcher\EventDispatcher */
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $eventDispatcher->dispatch(FetchMailEvents::FETCH_MAIL_START);

        $this->getContainer()->get('wiakowe_fetch.processing.planner')
            ->processMails($this->getContainer()->get('wiakowe_fetch.client'));

        $eventDispatcher->dispatch(FetchMailEvents::FETCH_MAIL_END);
    }
}
