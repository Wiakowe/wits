<?php

namespace Wiakowe\BreadcrumbBundle\Twig\Extension;

use Wiakowe\BreadcrumbBundle\Model\BreadcrumbModel;
/**
 * Created by JetBrains PhpStorm.
 * User: patxi1980
 * Date: 2/07/12
 * Time: 15:28
 * To change this template use File | Settings | File Templates.
 */
class BreadcrumbExtension extends \Twig_Extension
{
    protected $model;

    public function __construct(BreadcrumbModel $model)
    {
        $this->model = $model;
    }

    public function getGlobals()
    {
        return array(
          'breadcrumb' => $this->model
        );
    }

    public function getName()
    {
        return 'breadcrumb';
    }
}
