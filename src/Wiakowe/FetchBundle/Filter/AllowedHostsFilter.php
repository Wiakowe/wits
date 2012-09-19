<?php
namespace Wiakowe\FetchBundle\Filter;

use Fetch\Message;
use Fetch\Server;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class AllowedHostsFilter implements FilterInterface
{
    protected $allowedHosts = array();

    public function __construct(array $hosts)
    {
        $this->allowedHosts = $hosts;
    }

    public function valid(Message $message)
    {
        $mailHost = $message->getHeaders()->from[0]->host;

        return in_array($mailHost, $this->allowedHosts);
    }
}
