<?php

namespace Wits\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\ProjectBundle\Entity\Project;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Version;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class VersionController extends Controller
{
    public function editAction(Project $project, Version $version = null)
    {
        $isEdit = (boolean) $version;

        if (!$isEdit)  {
            $version = new Version();
        } else {
            $versionRepository = $this->getDoctrine()->getRepository('WitsVersionBundle:Version');
            if (!$versionRepository->checkVersionFromProject($version, $project)) {
                throw new ResourceNotFoundException();
            }
        }

        $form = $this->createFormBuilder($version)
            ->add('name')
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $version->setProject($project);

                $manager->persist($version);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'Your version has been edited' : 'Your version has been created');

                return $this->redirect($this->get('router')->generate('wits_version_show', array('project_id' => $project->getId(), 'version_id' => $version->getId())));
            }
        }

        return $this->render('WitsIssueBundle:Issue:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }

    public function showAction(Project $project, Version $version)
    {
        $versionRepository = $this->getDoctrine()->getRepository('WitsVersionBundle:Version');
        if (!$versionRepository->checkVersionFromProject($version, $project)) {
            throw new ResourceNotFoundException();
        }

        return $this->render('WitsProjectBundle:Version:show.html.twig',
            array(
                'project'   => $project,
                'version'   => $version
            )
        );
    }
}
