<?php
namespace Wits\IssueBundle\Mail\Processor;

use Fetch\Message;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class NewCommentProcessor extends AllowedHostProcessor
{
    public function isApplicable(Message $message)
    {
        if (!parent::isApplicable($message)) {
            return false;
        }

        return false;
    }

    /**
     * Processes the given message.
     *
     * @param \Fetch\Message $message
     */
    public function process(Message $message)
    {
        // TODO: Implement process() method.
    }
}
