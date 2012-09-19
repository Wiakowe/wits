<?php
namespace Wits\IssueBundle\Mail\Processor;

use Fetch\Message;
use Wiakowe\FetchBundle\Processor\MailProcessorInterface;
use Doctrine\ORM\EntityManager;
use Wits\UserBundle\Entity\User;
use Wits\IssueBundle\Entity\Issue;
use Wits\IssueBundle\Event\IssueCreateEvent;
use Wits\IssueBundle\Event\IssueEvents;
use Wits\HelperBundle\Util\MailUtil;
use Wits\HelperBundle\Util\StringUtil;
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

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
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

        $content = $message->getMessageBody();

        $content = StringUtil::normalizeNewlines($content);
        $content = MailUtil::removeSignature($content);

        $issue->setCreator($user);
        $issue->setName($message->getSubject());
        $issue->setDescription($content);
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
