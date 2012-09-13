<?php
namespace Wits\HelperBundle\Model;

use Wits\HelperBundle\Entity\BreadcrumbEntity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/**
 * @author Aitor Suso
 */
class BreadcrumbModel
{
    protected $breadcrumbEntries;

    public function __construct()
    {
        $this->breadcrumbEntries = array();

        $this->addEntry('label_home', 'wits_project_dashboard');
    }

    public function getEntries()
    {
        return $this->breadcrumbEntries;
    }

    public function addEntry($name, $link, $params = array())
    {
        $this->breadcrumbEntries[] = new BreadcrumbEntity($name, $link, $params);

        return $this;
    }
}
