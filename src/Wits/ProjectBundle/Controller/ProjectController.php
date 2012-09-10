<?php

namespace Wits\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('WitsProjectBundle:Project:dashboard.html.twig');
    }
}
