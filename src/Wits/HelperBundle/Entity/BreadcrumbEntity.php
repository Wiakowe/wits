<?php

namespace Wits\HelperBundle\Entity;
/**
 * Created by JetBrains PhpStorm.
 * User: patxi1980
 * Date: 2/07/12
 * Time: 16:07
 * To change this template use File | Settings | File Templates.
 */
class BreadcrumbEntity
{
    protected $link;

    protected $params;

    protected $name;

    public function __construct($name, $link, $params)
    {
        $this->link     = $link;
        $this->params   = $params;
        $this->name     = $name;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParams()
    {
        return $this->params;
    }

}
