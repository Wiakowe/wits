<?php

namespace Wits\UserBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Wits\UserBundle\Entity\User;

/**
 * Service to deal with all the operations related with the security component.
 */
class SecurityManager
{
    protected $securityContext;
    protected $eventDispatcher;
    protected $encoderFactory;
    protected $request;

    public function __construct(SecurityContext $securityContext, EncoderFactoryInterface $encoderFactory, EventDispatcher $eventDispatcher, Request $request)
    {
        $this->securityContext = $securityContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->request         = $request;
        $this->encoderFactory  = $encoderFactory;
    }

    /**
     * Logs in as the given user, without going through the login page.
     *
     * @param UserInterface $user
     * @param string $providerKey
     */
    public function loginUserWithoutCredentials(UserInterface $user, $providerKey = 'main')
    {
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->securityContext->setToken($token);
        $event = new InteractiveLoginEvent($this->request, $token);
        $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }

    /**
     * Gets the login error, if any.
     *
     * @param Request $request
     * @return mixed|null
     */
    public function getLoginError(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $error;
    }

    /**
     * Gets the last username introduced by the user.
     *
     * @param Request $request
     * @return mixed
     */
    public function getLoginLastUsername(Request $request)
    {
        $session = $request->getSession();

        return $session->get(SecurityContext::LAST_USERNAME);
    }

    /**
     * Encodes the user password
     *
     * @param \Wits\UserBundle\Entity\User $user
     * @param string $plainPassword the plain password to be encoded. If null, it'll be retrieved from the plain password
     * property in the user object.
     * @return string
     */
    protected function encodeUserPassword(User $user, $plainPassword = null)
    {
        if (null === $plainPassword) {
            $plainPassword = $user->getPlainPassword();
        }

        return $this->encoderFactory->getEncoder($user)
            ->encodePassword($plainPassword, $user->getSalt());
    }

    /**
     * Verify that the password given is the same as the user.
     *
     * @param \Wits\UserBundle\Entity\User $user
     * @param string $plainPassword
     *
     * @return bool
     */
    public function checkPassword(User $user, $plainPassword)
    {
        return $user->getPassword() === $this->encodeUserPassword($user, $plainPassword);
    }

    /**
     * Sets the user password to the one given, or to the one set in plain password, if there is any.
     *
     * @param \Wits\UserBundle\Entity\User $user
     * @param $plainPassword
     */
    public function setUserPassword(User $user, $plainPassword = null)
    {
        $user->setPassword($this->encodeUserPassword($user, $plainPassword));
    }
}
