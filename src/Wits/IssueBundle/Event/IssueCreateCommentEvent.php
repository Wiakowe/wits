<?php

namespace Wits\IssueBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Wits\IssueBundle\Entity\Issue;
use Wits\IssueBundle\Entity\Comment;

class IssueCreateCommentEvent extends Event
{
    /**
     * @var \Wits\IssueBundle\Entity\Issue
     */
    protected $issue;

    /**
     * @var \Wits\IssueBundle\Entity\Comment
     */
    protected $comment;

    /**
     * @param \Wits\IssueBundle\Entity\Comment $comment
     * @param \Wits\IssueBundle\Entity\Issue $issue
     */
    public function __construct(Comment $comment, Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * @return \Wits\IssueBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * @return \Wits\IssueBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
}
