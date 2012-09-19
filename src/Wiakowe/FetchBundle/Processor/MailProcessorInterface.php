<?php
namespace Wiakowe\FetchBundle\Processor;

use Fetch\Message;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
interface MailProcessorInterface
{
    /**
     * Determines if the given message can be processed by the current processor.
     *
     * @param \Fetch\Message $message
     *
     * @return boolean
     */
    public function apply(Message $message);
}
