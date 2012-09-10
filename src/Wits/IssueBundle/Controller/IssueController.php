<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Wits\IssueBundle\Entity\Issue;

class IssueController extends Controller
{
    public function editAction(Issue $issue = null)
    {
        $isEdit = (boolean) $issue;

        if (!$isEdit)  {
            $issue = new Issue();
        }

        $form = $this->createFormBuilder($issue)
            ->add('name')
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($issue);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'Your issue has been edited' : 'Your issue has been created');

                return $this->redirect($this->get('router')->generate('wits_issue_show', array('issue_id' => $issue->getId())));
            }
        }

        return $this->render('WitsProjectBundle:Project:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }
}
