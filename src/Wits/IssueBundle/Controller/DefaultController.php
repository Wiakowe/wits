<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WitsIssueBundle:Default:index.html.twig', array('name' => $name));
    }
}
