<?php

namespace Wits\IssueBundle\EventListener;

use Wits\IssueBundle\Event\IssueCreateEvent;
use Symfony\Component\Translation\TranslatorInterface;
use \Swift_Mailer;
use Symfony\Component\Templating\EngineInterface;


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

    public function onIssueCreation(IssueCreateEvent $event)
    {
        $issue = $event->getIssue();

        $message = \Swift_Message::newInstance()
            ->setSubject('[#'.$issue->getProject()->getIdentifier().'-'.$issue->getId().'] '.$this->trans->trans('label_mail_issue_created').': '.$issue->getName())
            ->setFrom($this->notificationMail)
            ->setReplyTo($this->notificationMail)
            ->setTo($issue->getCreator()->getEmail())
            ->setBcc($issue->getProject()->getLeader()->getEmail())
            //->setBody('Issue '.$issue->getId().' has been created')
            ->setBody($this->templating->render('IssueBundle:Mail:issue_create.html.twig', array($issue)))
        ;

        $this->mailer->send($message);

        //var_dump($message);
        //var_dump($this->mailer->send($message));
        //die();
    }

}
