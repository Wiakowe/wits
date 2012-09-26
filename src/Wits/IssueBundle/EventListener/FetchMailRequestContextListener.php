<?php
namespace Wits\IssueBundle\EventListener;

use Symfony\Component\Routing\RouterInterface;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class FetchMailRequestContextListener
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    protected $host;

    protected $debug;

    public function __construct(RouterInterface $router, $host, $debug = false)
    {
        $this->router = $router;
        $this->host   = $host;
        $this->debug  = $debug;
    }

    public function onFetchMailStart()
    {
        $this->router->getContext()->setHost($this->host);

        if ($this->debug) {
            $this->router->getContext()->setBaseUrl('/app_dev.php');
        }
    }
}
