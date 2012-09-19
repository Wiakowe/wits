<?php
namespace Wiakowe\FetchBundle\Processor;

use Fetch\Message;
use Fetch\Server;
use Wiakowe\FetchBundle\Filter\FilterInterface;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MailProcessingPlanner
{
    protected $processors = array();
    protected $filters    = array();
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

    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
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

        foreach ($this->filters as $filter) {
            /** @var $filter FilterInterface */
            if (!$filter->valid($message)) {
                return false;
            }
        }

        foreach ($this->processors as $processorsArray) {
            foreach ($processorsArray as $processor) {
                /** @var $processor MailProcessorInterface */
                if ($processor->apply($message)) {
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
