<?php
namespace Wiakowe\FetchBundle\Processor;

use Fetch\Message;
use Fetch\Server;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MailProcessingPlanner
{
    protected $processors = array();
    protected $sorted     = true;

    protected $processedLabel;

    public function __construct($processedLabel = 'Processed')
    {
        $this->processedLabel = $processedLabel;
    }

    public function addProcessor(MailProcessorInterface $processor, $priority = 0)
    {
        if (!array_key_exists($priority, $this->processors)) {
            $this->processors[$priority] = array();
        }

        $this->processors[$priority][] = $processor;

        $this->sorted = false;
    }

    public function processMails(Server $server)
    {
        $server->setMailBox('INBOX');
        foreach ($server->getMessages() as $message) {
            if ($this->processMail($message)) {
                $this->markProcessed($server, $message);
            }
        }
        $server->expunge();
    }

    protected function processMail(Message $message)
    {
        if (!$this->sorted) {
            krsort($this->processors);
        }

        foreach ($this->processors as $processorsArray) {
            foreach ($processorsArray as $processor) {
                /** @var $processor MailProcessorInterface */
                if ($processor->isApplicable($message)) {
                    $processor->process($message);
                    return true;
                }
            }
        }

        return false;
    }

    protected function markProcessed(Server $server, Message $message)
    {
        if (!($server->hasMailBox($this->processedLabel) || $server->createMailBox($this->processedLabel))) {
            throw new \RuntimeException('The mailbox couldn\'t be found nor created.');
        }

        $message->moveToMailbox($this->processedLabel);
    }
}
