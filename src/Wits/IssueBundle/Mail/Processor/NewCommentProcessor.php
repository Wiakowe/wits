<?php
namespace Wits\IssueBundle\Mail\Processor;

use Fetch\Message;
use Wiakowe\FetchBundle\Processor\MailProcessorInterface;
use Doctrine\ORM\EntityManager;
use Wits\UserBundle\Entity\User;
use Wits\IssueBundle\Entity\Issue;
use Wits\IssueBundle\Event\IssueCreateCommentEvent;
use Wits\IssueBundle\Event\IssueEvents;
use Wits\IssueBundle\Entity\Comment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Wits\HelperBundle\Util\MailUtil;
use Wits\HelperBundle\Util\StringUtil;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class NewCommentProcessor implements MailProcessorInterface
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
        $subject = $message->getSubject();

        if (!preg_match('/\[#(?P<project_identifier>[A-Z]{1,3})-(?P<issue_id>\d+)\]/', $subject, $matches)) {
            return false;
        }

        $issue = $this->entityManager
            ->createQueryBuilder()
            ->select('i')
            ->from('WitsIssueBundle:Issue', 'i')
            ->innerJoin('i.project', 'p')
            ->andWhere('p.identifier = :project_identifier')
            ->andWhere('i.id = :issue_id')
            ->setParameter('issue_id', $matches['issue_id'])
            ->setParameter('project_identifier', $matches['project_identifier'])
            ->getQuery()->getOneOrNullResult()
        ;

        if (!$issue) {
            return false;
        }

        $senderAddressFrom = $message->getAddresses('from');
        $senderAddress     = $senderAddressFrom['address'];

        $user = $this->entityManager
            ->getRepository('WitsUserBundle:User')
            ->findOneByEmail($senderAddress);

        if (!$user) {
            $user = new User;

            $user->setEmail($senderAddress);
            $user->addRole('ROLE_REPORTER');

            $this->entityManager->persist($user);
        }

        $commentContent = $message->getMessageBody();

        $commentContent = StringUtil::normalizeNewlines($commentContent);
        $commentContent = MailUtil::removeReply($commentContent);
        $commentContent = MailUtil::removeSignature($commentContent);

        $comment = new Comment();

        $comment->setComment($commentContent);
        $comment->setUser($user);
        $comment->setIssue($issue);

        $this->entityManager->persist($comment);

        $event = new IssueCreateCommentEvent($comment, $issue);

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(IssueEvents::ISSUE_COMMENT, $event);

        return true;
    }
}
