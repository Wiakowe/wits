<?php

namespace Wits\IssueBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Wits\IssueBundle\Entity\Issue;

class IssueCreateEvent extends Event
{
    protected $issue;

    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    public function getIssue()
    {
        return $this->issue;
    }
}
