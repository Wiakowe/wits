<?php

namespace Wits\IssueBundle\EventListener;

use Wits\IssueBundle\Event\IssueCreateEvent;
use Wits\IssueBundle\Event\IssueCreateCommentEvent;
use Wits\IssueBundle\Event\IssueEditEvent;
use Symfony\Component\Translation\TranslatorInterface;
use \Swift_Mailer;
use Symfony\Component\Templating\EngineInterface;
use Wits\IssueBundle\Entity\Issue;


class NotificationListener
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var string
     */
    protected $notificationMail;


    public function __construct(TranslatorInterface $translator, Swift_Mailer $mailer, EngineInterface $templating, $notificationMail)
    {
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->notificationMail = $notificationMail;
    }

    protected function getIssueAssignee(Issue $issue)
    {
        return $issue->getAssignee() ?: $issue->getProject()->getLeader();
    }

    public function onIssueCreation(IssueCreateEvent $event)
    {
        $issue = $event->getIssue();

        $assignee = $this->getIssueAssignee($issue);

        $message = \Swift_Message::newInstance()
            ->setSubject('[#'.$issue->getProject()->getIdentifier().'-'.$issue->getId().'] '.$this->translator->trans('label_mail_issue_created').': '.$issue->getName())
            ->setFrom($this->notificationMail)
            ->setReplyTo($this->notificationMail)
            ->setTo($issue->getCreator()->getEmail())
            ->setBcc($assignee->getEmail())
            ->setBody($this->templating->render('WitsIssueBundle:Mail:issue_create.html.twig', array('issue' => $issue)))
            ->setContentType('text/html')
        ;

        $this->mailer->send($message);
    }

    public function onIssueCommentCreation(IssueCreateCommentEvent $event)
    {
        $issue = $event->getIssue();
        $comment = $event->getComment();

        $assignee = $this->getIssueAssignee($issue);

        $emailTo = array();

        if ($comment->getUser()->getEmail() != $assignee->getEmail()) {
            $emailTo[] = $assignee->getEmail();
        }

        if ($comment->getUser()->getEmail() != $issue->getCreator()->getEmail()) {
            $emailTo[] = $issue->getCreator()->getEmail();
        }

        $emailTo = array_unique($emailTo);

        $message = \Swift_Message::newInstance()
            ->setSubject('[#'.$issue->getProject()->getIdentifier().'-'.$issue->getId().'] '.$this->translator->trans('label_mail_issue_commented').': '.$issue->getName())
            ->setFrom($this->notificationMail)
            ->setReplyTo($this->notificationMail)
            ->setBody($this->templating->render('WitsIssueBundle:Mail:issue_comment.html.twig', array('issue' => $issue, 'comment' => $comment)))
            ->setContentType('text/html')
        ;

        $this->sendMessages($message, $emailTo);
    }

    public function onIssueEdit(IssueEditEvent $event)
    {
        $issue = $event->getIssue();
        $issueOld = $event->getIssueOld();
        $editor = $event->getUser();

        $assignee = $this->getIssueAssignee($issue);

        $emailTo = array();

        if ($editor->getEmail() != $assignee->getEmail()) {
            $emailTo[] = $assignee->getEmail();
        }

        if ($editor->getEmail() != $issue->getCreator()->getEmail()) {
            $emailTo[] = $issue->getCreator()->getEmail();
        }

        $emailTo = array_unique($emailTo);

        $message = \Swift_Message::newInstance()
            ->setSubject('[#'.$issue->getProject()->getIdentifier().'-'.$issue->getId().'] '.$this->translator->trans('label_mail_issue_updated').': '.$issue->getName())
            ->setFrom($this->notificationMail)
            ->setReplyTo($this->notificationMail)
            ->setBody($this->templating->render('WitsIssueBundle:Mail:issue_edit.html.twig', array('issue' => $issue, 'issueOld' => $issueOld)))
            ->setContentType('text/html');

        $this->sendMessages($message, $emailTo);

    }

    protected function sendMessages($message, $emails)
    {
        foreach ($emails as $email) {
            $sendMessage = clone $message;
            $sendMessage->setTo($email);
            $this->mailer->send($sendMessage);
        }
    }

}
