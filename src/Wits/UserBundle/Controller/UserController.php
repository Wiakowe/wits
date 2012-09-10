<?php

namespace Wits\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\UserBundle\Entity\User;

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

                $user->addRole('ROLE_USER');

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
}
