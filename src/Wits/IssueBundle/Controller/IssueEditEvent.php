<?php

namespace Wits\IssueBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Wits\IssueBundle\Entity\Issue;

class IssueEditEvent
{
    /**
     * @var \Wits\IssueBundle\Entity\Issue
     */
    protected $issue;

    /**
     * @var \Wits\IssueBundle\Entity\Issue
     */
    protected $issueOld;

    public function __construct(Issue $issue, Issue $issueOld)
    {
        $this->issue = $issue;

        $this->issueOld = $issueOld;
    }

    public function getIssue()
    {
        return $this->issue;
    }

    public function getIssueOld()
    {
        return $this->issueOld;
    }
}
