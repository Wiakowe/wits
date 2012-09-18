<?php
namespace Wits\IssueBundle\Mail\Processor;

use Fetch\Message;
use Wiakowe\FetchBundle\Processor\MailProcessorInterface;
use Doctrine\ORM\EntityManager;
use Wits\UserBundle\Entity\User;
use Wits\IssueBundle\Entity\Issue;
use Wits\IssueBundle\Events\IssueCreateEvent;
use Wits\IssueBundle\Events\IssueEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class NewIssueProcessor implements MailProcessorInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    protected $eventDispatcher;

    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager   = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function apply(Message $message)
    {
        $senderAddress = $message->getAddresses('from')['address'];

        $user = $this->entityManager
            ->getRepository('WitsUserBundle:User')
            ->findOneByEmail($senderAddress);

        if (!$user) {
            $user = new User;

            $user->setEmail($senderAddress);
            $user->addRole('ROLE_REPORTER');

            $this->entityManager->persist($user);
        }

        $issue = new Issue;

        /** @var $project \Wits\ProjectBundle\Entity\Project */
        $project = $this->entityManager
            ->getRepository('WitsProjectBundle:Project')
            ->findOneBy(array());

        $issue->setCreator($user);
        $issue->setName($message->getSubject());
        $issue->setDescription($message->getMessageBody());
        $issue->setProject($project);

        $this->entityManager->persist($issue);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            IssueEvents::ISSUE_CREATE,
            new IssueCreateEvent($issue)
        );

        return true;
    }
}
