<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Wits\IssueBundle\Event\IssueCreateEvent;
use Wits\IssueBundle\Event\IssueEditEvent;
use Wits\IssueBundle\Event\IssueEvents;

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
            $issueOld = clone $issue;

            if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_EDIT')) {
                throw new AccessDeniedException();
            }

            $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');
            if (!$issueRepository->checkIssueFromProject($issue, $project)) {
                throw new ResourceNotFoundException();
                throw new ResourceNotFoundException();
            }
        }

        $formBuilder = $this->createFormBuilder($issue)
            ->add('name', null, array('label' => 'label_issue_name', 'attr' => array('class' => 'input-xxlarge')))
            ->add('description', 'textarea', array('label' => 'label_issue_description', 'attr' => array('rows' => 3, 'class' => 'input-xxlarge')))

        ;
        if ($this->get('security.context')->isGranted('ROLE_ISSUE_SET_VERSION')) {
            $formBuilder->add('version', null, array('label' => 'label_issue_version', 'required' => false, 'attr' => array('class' => 'input-large')));
        }

        if ($this->get('security.context')->isGranted('ROLE_ISSUE_ASSIGN')) {
            $formBuilder->add('assignee', null, array('label' => 'label_issue_assignee', 'attr' => array('class' => 'input-large')));
        }

        if ($this->get('security.context')->isGranted('ROLE_ISSUE_EDIT_STATUS')) {
            $formBuilder->add('status', 'choice', array('choices' => Issue::$statusList, 'label' => 'label_issue_status', 'attr' => array('class' => 'input-large')));
        }

        if ($this->get('security.context')->isGranted('ROLE_ISSUE_SET_PRIORITY')) {
            $formBuilder->add('priority', 'choice', array('choices' => Issue::$priorityList, 'label' => 'label_issue_priority', 'attr' => array('class' => 'input-large')));
        }

        if ($this->get('security.context')->isGranted('ROLE_ISSUE_EDIT_HOURS')) {
            $formBuilder->add('estimatedHours', 'integer', array('label' => 'label_issue_hours_estimated', 'attr' => array('class' => 'input-mini', 'min' => 0)));
            $formBuilder->add('spentHours', 'integer', array('label' => 'label_issue_hours_spent', 'attr' => array('class' => 'input-mini', 'min' => 0)));
        }

        $breadcrumb = $this->get('wiakowe.breadcrumb');
        $breadcrumb->addEntry('label_issues', 'wits_issue_list', array('project_id' => $project->getId()));
        if ($isEdit) {
            $breadcrumb->addEntry($issue->getName(), 'wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));
            $breadcrumb->addEntry('label_edit', 'wits_issue_edit', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));
        } else {
            $breadcrumb->addEntry('label_create', 'wits_issue_new', array('project_id' => $project->getId()));
        }


        $form = $formBuilder->getForm();


        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                if (!$issue->getAssignee()) {
                    $issue->setAssignee($project->getLeader());
                }

                $issue->setProject($project);
                $issue->setCreator($this->getUser());

                $manager->persist($issue);
                $manager->flush();

                $dispatcher = $this->get('event_dispatcher');
                if (!$isEdit) {
                    $event = new IssueCreateEvent($issue);
                    $dispatcher->dispatch(IssueEvents::ISSUE_CREATE, $event);
                } else {
                    $event = new IssueEditEvent($issue, $issueOld);
                    $dispatcher->dispatch(IssueEvents::ISSUE_EDIT, $event);
                }

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'label_issue_edited' : 'label_issue_created');

                return $this->redirect($this->get('router')->generate('wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId())));
            }
        }

        return $this->render('WitsIssueBundle:Issue:edit.html.twig',
            array(
                'issue'     => $issue,
                'project'   => $project,
                'form'      => $form->createView()
            )
        );
    }

    public function listAction(Project $project)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_LIST')) {
            throw new AccessDeniedException();
        }

        $breadcrumb = $this->get('wiakowe.breadcrumb');
        $breadcrumb->addEntry('label_issues', 'wits_issue_list', array('project_id' => $project->getId()));

        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');

        $issues = $issueRepository->findBy(array('project' => $project->getId()));

        //get versions
        $versions = $this->getDoctrine()->getRepository('WitsProjectBundle:Version')->findBy(array('project' => $project->getId()));

        return $this->render('WitsIssueBundle:Issue:list.html.twig',
            array(
                'project'   => $project,
                'issues'    => $issues,
                'versions'  => $versions,
            )
        );
    }

    public function createdListAction(Project $project)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_LIST')) {
            throw new AccessDeniedException();
        }

        $breadcrumb = $this->get('wiakowe.breadcrumb');
        $breadcrumb->addEntry('label_issues', 'wits_issue_list', array('project_id' => $project->getId()));

        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');

        $issues = $issueRepository->getCreatedIssues($project, $this->getUser());

        //get versions
        $versions = $this->getDoctrine()->getRepository('WitsProjectBundle:Version')->findBy(array('project' => $project->getId()));

        return $this->render('WitsIssueBundle:Issue:list.html.twig',
            array(
                'project'   => $project,
                'issues'    => $issues,
                'versions'  => $versions,
                'created'   => true,
            )
        );
    }

    public function assignedListAction(Project $project)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_LIST')) {
            throw new AccessDeniedException();
        }

        $breadcrumb = $this->get('wiakowe.breadcrumb');
        $breadcrumb->addEntry('label_issues', 'wits_issue_list', array('project_id' => $project->getId()));

        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');

        $issues = $issueRepository->getAssignedIssues($project, $this->getUser());

        //get versions
        $versions = $this->getDoctrine()->getRepository('WitsProjectBundle:Version')->findBy(array('project' => $project->getId()));

        return $this->render('WitsIssueBundle:Issue:list.html.twig',
            array(
                'project'   => $project,
                'issues'    => $issues,
                'versions'  => $versions,
                'assigned'  => true,
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

        $breadcrumb = $this->get('wiakowe.breadcrumb');
        $breadcrumb->addEntry('label_issues', 'wits_issue_list', array('project_id' => $project->getId()));
        $breadcrumb->addEntry($issue->getName(), 'wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));

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
