<?php
namespace Wiakowe\FetchBundle\Filter;

use Fetch\Message;
use Fetch\Server;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
interface FilterInterface
{
    /**
     * @param \Fetch\Message $message
     *
     * @return boolean
     */
    public function valid(Message $message);
}
