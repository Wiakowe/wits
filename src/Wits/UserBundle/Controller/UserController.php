<?php

namespace Wits\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            ->add('email', 'email', array('label' => 'E-mail'))
            ->add('name')
            ->add('surname')
            ->add('plainPassword', 'repeated', array(
                'first_name' => 'password',
                'second_name' => 'password_repeat',
                'first_options' => array('label' => 'Contraseña'),
                'second_options' => array('label' => 'Repetir contraseña'),
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

    public function listAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USERS_LIST')) {
            throw new AccessDeniedException();
        }

        $userRepository = $this->getDoctrine()->getRepository('WitsUserBundle:User');

        $users = $userRepository->findAll();

        return $this->render('WitsUserBundle:User:list.html.twig', array(
            'users'     => $users
        ));
    }


    public function editAction(User $user = null)
    {
        $isEdit = (boolean) $user;

        if (!$isEdit)  {
            $user = new User();
            if (false === $this->get('security.context')->isGranted('ROLE_USERS_CREATE')) {
                throw new AccessDeniedException();
            }
        } else {
            if (false === $this->get('security.context')->isGranted('ROLE_USERS_EDIT')) {
                throw new AccessDeniedException();
            }
        }

        $rolesToDisplay = array(
            'ROLE_SUPER_ADMIN',
            'ROLE_PROJECT_LEADER',
            'ROLE_DEVELOPER',
            'ROLE_REPORTER'
        );

        $rolesChoice = array_combine($rolesToDisplay, $rolesToDisplay);

        $form = $this->createFormBuilder($user)
            ->add('email', 'email')
            ->add('name')
            ->add('surname')
            ->add('plainPassword', 'repeated', array(
                'required'  => !$isEdit,
                'first_name' => 'password',
                'second_name' => 'password_repeat',
                'first_options' => array('label' => 'Contraseña'),
                'second_options' => array('label' => 'Repetir contraseña'),
                'type' => 'password'
                )
            )
            ->add('roles', 'choice', array('choices' => $rolesChoice, 'expanded' => true, 'multiple' => true))
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

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'User has been edited' : 'User has been created');

                return $this->redirect($this->get('router')->generate('wits_users_list'));
            }
        }

        return $this->render('WitsUserBundle:User:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }

    public function editSelfAction()
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('email', 'email')
            ->add('name')
            ->add('surname')
            ->add('plainPassword', 'repeated', array(
                'required'  => false,
                'first_name' => 'password',
                'second_name' => 'password_repeat',
                'first_options' => array('label' => 'Contraseña'),
                'second_options' => array('label' => 'Repetir contraseña'),
                'type' => 'password'
            )
        )
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


                $this->getRequest()->getSession()->getFlashBag()->add('success', 'User has been edited');

                return $this->redirect($this->get('router')->generate('wits_user_self_edit'));
            }
        }

        return $this->render('WitsUserBundle:User:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }

}
