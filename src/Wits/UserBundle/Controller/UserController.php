<?php

namespace Wits\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Wits\ProjectBundle\Entity\Project;

class UserController extends Controller
{

    public function loginAction()
    {
        $securityManager = $this->get('wits.security_manager');
        /* @var \Wits\UserBundle\Service\SecurityManager $securityManager */

        $request = $this->getRequest();

        return $this->render('WitsUserBundle:User:login.html.twig', array(
            'last_username' => $securityManager->getLoginLastUsername($request),
            'error'         => $securityManager->getLoginError($request),
        ));

        return $this->render('WitsUserBundle:User:login.html.twig');
    }

    public function registerAction()
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('email', 'email', array('label' => 'label_user_email'))
            ->add('name', null, array('label' => 'label_user_name'))
            ->add('surname', null, array('label' => 'label_user_surname'))
            ->add('plainPassword', 'repeated', array(
                'first_name' => 'password',
                'second_name' => 'password_repeat',
                'first_options' => array('label' => 'label_user_password'),
                'second_options' => array('label' => 'label_user_password_repeat'),
                'type' => 'password'
                )
            )
            ->getForm()
        ;

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            $manager = $this->getDoctrine()->getManager();

            if ($form->isValid()) {

                $securityManager = $this->get('wits.security_manager');
                /* @var \Wits\UserBundle\Service\SecurityManager $securityManager */

                $manager = $this->getDoctrine()->getManager();

                $securityManager->setUserPassword($user);

                $user->addRole('ROLE_REPORTER');

                $securityManager->loginUserWithoutCredentials($user);

                $manager->persist($user);
                $manager->flush();

                return $this->redirect($this->generateUrl('wits_project_dashboard'));
            }
        }

        return $this->render('WitsUserBundle:User:register.html.twig', array(
            'form'          => $form->createView(),
        ));


    }

    public function listAction(Project $project)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USERS_LIST')) {
            throw new AccessDeniedException();
        }

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry('label_users', 'wits_users_list', array('project_id' => $project->getId()));

        $userRepository = $this->getDoctrine()->getRepository('WitsUserBundle:User');

        $users = $userRepository->findAll();

        return $this->render('WitsUserBundle:User:list.html.twig', array(
            'users'     => $users
        ));
    }


    public function editAction(User $user = null, Project $project)
    {
        $isEdit = (boolean) $user;

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry('label_users', 'wits_users_list', array('project_id' => $project->getId()));


        if (!$isEdit)  {
            $user = new User();
            if (false === $this->get('security.context')->isGranted('ROLE_USERS_CREATE')) {
                throw new AccessDeniedException();
            }
            $breadcrumb->addEntry('label_create', 'wits_users_new', array('project_id' => $project->getId()));
        } else {
            if (false === $this->get('security.context')->isGranted('ROLE_USERS_EDIT')) {
                throw new AccessDeniedException();
            }
            $breadcrumb->addEntry($user->getUsername(), 'wits_users_edit', array('user_id' => $user->getId(), 'project_id', $project->getId()));

        }

        $rolesToDisplay = array(
            'ROLE_SUPER_ADMIN',
            'ROLE_PROJECT_LEADER',
            'ROLE_DEVELOPER',
            'ROLE_REPORTER'
        );

        $rolesChoice = array_combine($rolesToDisplay, $rolesToDisplay);

        $form = $this->createFormBuilder($user)
            ->add('email', 'email', array('label' => 'label_user_email'))
            ->add('name', null, array('label' => 'label_user_name'))
            ->add('surname', null, array('label' => 'label_user_surname'))
            ->add('plainPassword', 'repeated', array(
                'required'  => !$isEdit,
                'first_name' => 'password',
                'second_name' => 'password_repeat',
                'first_options' => array('label' => 'label_user_password'),
                'second_options' => array('label' => 'label_user_password_repeat'),
                'type' => 'password'
                )
            )
            ->add('roles', 'choice', array('choices' => $rolesChoice, 'expanded' => true, 'multiple' => true, 'label' => 'label_user_roles'))
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                if ($form->getData()->getPlainPassword()) {

                    $securityManager = $this->get('wits.security_manager');
                    /* @var \Wits\UserBundle\Service\SecurityManager $securityManager */

                    $securityManager->setUserPassword($user);
                }

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($user);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'label_user_edited' : 'label_user_created');

                return $this->redirect($this->get('router')->generate('wits_users_list', array('project_id' => $project->getId())));
            }
        }

        return $this->render('WitsUserBundle:User:edit.html.twig',
            array(
                'user'  => $user,
                'form'  => $form->createView()
            )
        );
    }

    public function editSelfAction(Project $project)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('email', 'email', array('label' => 'label_user_email'))
            ->add('name', null, array('label' => 'label_user_name'))
            ->add('surname', null, array('label' => 'label_user_surname'))
            ->add('plainPassword', 'repeated', array(
                'required'  => false,
                'first_name' => 'password',
                'second_name' => 'password_repeat',
                'first_options' => array('label' => 'label_user_password'),
                'second_options' => array('label' => 'label_user_password_repeat'),
                'type' => 'password'
            )
        )
            ->getForm()
        ;

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry('label_user_self_edit', 'wits_user_self_edit', array('project_id' => $project->getId()));

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                if ($form->getData()->getPlainPassword()) {

                    $securityManager = $this->get('wits.security_manager');
                    /* @var \Wits\UserBundle\Service\SecurityManager $securityManager */

                    $securityManager->setUserPassword($user);
                }

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($user);
                $manager->flush();


                $this->getRequest()->getSession()->getFlashBag()->add('success', 'label_user_edited');

                return $this->redirect($this->get('router')->generate('wits_user_self_edit', array('project_id' => $project->getId())));
            }
        }

        return $this->render('WitsUserBundle:User:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }

}
