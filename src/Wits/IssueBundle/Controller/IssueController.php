<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class IssueController extends Controller
{
    public function editAction(Project $project, Issue $issue = null)
    {
        $isEdit = (boolean) $issue;

        if (!$isEdit)  {
            $issue = new Issue();
            if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_CREATE')) {
                throw new AccessDeniedException();
            }

        } else {
            if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_EDIT')) {
                throw new AccessDeniedException();
            }

            $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');
            if (!$issueRepository->checkIssueFromProject($issue, $project)) {
                throw new ResourceNotFoundException();
            }
        }

        $formBuilder = $this->createFormBuilder($issue)
            ->add('name')
            ->add('description', 'textarea')

        ;
        if ($this->get('security.context')->isGranted('ROLE_ISSUE_SET_VERSION')) {
            $formBuilder->add('version', null, array('required' => false));
        }

        if ($this->get('security.context')->isGranted('ROLE_ISSUE_ASSIGN')) {
            $formBuilder->add('assignee', null, array('required' => false));
        }

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry('Issues', 'wits_issue_list', array('project_id' => $project->getId()));
        if ($isEdit) {
            $breadcrumb->addEntry($issue->getName(), 'wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));
            $breadcrumb->addEntry('Editar', 'wits_issue_edit', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));
        } else {
            $breadcrumb->addEntry('Crear', 'wits_issue_new', array('project_id' => $project->getId()));
        }


        $form = $formBuilder->getForm();


        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $issue->setProject($project);
                $issue->setCreator($this->getUser());

                $manager->persist($issue);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'Your issue has been edited' : 'Your issue has been created');

                return $this->redirect($this->get('router')->generate('wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId())));
            }
        }

        return $this->render('WitsIssueBundle:Issue:edit.html.twig',
            array(
                'issue' => $issue,
                'form'  => $form->createView()
            )
        );
    }

    public function listAction(Project $project)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_LIST')) {
            throw new AccessDeniedException();
        }

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry('Issues', 'wits_issue_list', array('project_id' => $project->getId()));

        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');

        $issues = $issueRepository->findBy(array('project' => $project->getId()));

        return $this->render('WitsIssueBundle:Issue:list.html.twig',
            array(
                'project'   => $project,
                'issues'    => $issues,
            )
        );
    }

    public function showAction(Project $project, Issue $issue)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_SHOW')) {
            throw new AccessDeniedException();
        }

        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');
        if (!$issueRepository->checkIssueFromProject($issue, $project)) {
            throw new ResourceNotFoundException();
        }

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry('Issues', 'wits_issue_list', array('project_id' => $project->getId()));
        $breadcrumb->addEntry($issue->getName(), 'wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));
        $breadcrumb->addEntry('Ver', 'wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));

        $commentRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Comment');

        $comments = $commentRepository->findBy(array('issue' => $issue->getId()));

        return $this->render('WitsIssueBundle:Issue:show.html.twig',
            array(
                'project'   => $project,
                'issue'     => $issue,
                'comments'  => $comments
            )
        );
    }
}
