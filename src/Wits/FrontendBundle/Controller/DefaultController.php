<?php

namespace Wits\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->redirect($this->get('router')->generate('wits_project_dashboard'));
    }
}
