<?php

namespace Wits\IssueBundle\Events;

final class IssueEvents
{
    /**
     * The issue.creaate event is thrown each time a new issue is created.
     */
    const ISSUE_CREATE = 'issue.create';

    /**
     * The issue.comment event is thrown for each new comment on a issue
     */
    const ISSUE_COMMENT = 'issue.comment';

    /**
     * The issue.edit event is thrown when an issue is edited.
     */
    const ISSUE_EDIT = 'issue.edit';

}
