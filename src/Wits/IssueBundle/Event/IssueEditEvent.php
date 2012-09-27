<?php

namespace Wits\IssueBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Wits\IssueBundle\Entity\Issue;
use Wits\UserBundle\Entity\User;

class IssueEditEvent extends Event
{
    /**
     * @var \Wits\IssueBundle\Entity\Issue
     */
    protected $issue;

    /**
     * @var \Wits\IssueBundle\Entity\Issue
     */
    protected $issueOld;

    /**
     * @var \Wits\UserBundle\Entity\User
     */
    protected $user;

    public function __construct(Issue $issue, Issue $issueOld, User $user)
    {
        $this->issue = $issue;

        $this->issueOld = $issueOld;

        $this->user = $user;
    }

    public function getIssue()
    {
        return $this->issue;
    }

    public function getIssueOld()
    {
        return $this->issueOld;
    }

    public function getUser()
    {
        return $this->user;
    }
}
