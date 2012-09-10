<?php

namespace FashionWeb\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use FashionWeb\UserBundle\Entity\User;

// Required to be able to acceed to the symfony container.
use Symfony\Component\DependencyInjection\ContainerAware;

class AdminUser extends ContainerAware implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $adminUser = new User;

        $adminUser->setEmail('admin@wits.com');
        $adminUser->addRole('ROLE_SUPER_ADMIN');

        $plainPassword = 'admin2012';

        $password = $this->container->get('security.encoder_factory')
            ->getEncoder($adminUser)
            ->encodePassword($plainPassword, $adminUser->getSalt());


        $adminUser->setPassword($password);

        $manager->persist($adminUser);
        $manager->flush();
    }


}
