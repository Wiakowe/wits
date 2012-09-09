<?php

namespace Wits\HelperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WitsHelperBundle:Default:index.html.twig', array('name' => $name));
    }
}
