<?php

namespace Wits\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    const PASSWORD_RESET_CODE_DURATION = '+2day';

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(groups={"preRegistration"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $salt;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * The plain user password. Must never be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected $passwordResetCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="timestamp", nullable=true, columnDefinition="TIMESTAMP NULL")
     */
    protected $passwordResetCodeExpires;

    public function __construct()
    {
        $this->roles = array();
        $this->salt  = md5(uniqid(null, true));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return void
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Sets the encoded password.
     *
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function addRole($role)
    {
        if(false === array_search($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole($role)
    {
        if(true === $key = array_search($role, $this->roles)) {
            unset($this->roles[$key]);
        }
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return trim($this->getName().' '.$this->getSurname());
    }

    /**
     * @param string $passwordResetCode
     */
    public function setPasswordResetCode($passwordResetCode)
    {
        $this->passwordResetCode = $passwordResetCode;
        $this->passwordResetCodeExpires = new \DateTime(self::PASSWORD_RESET_CODE_DURATION);
    }

    /**
     * @return string
     */
    public function getPasswordResetCode()
    {
        return $this->passwordResetCode;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordResetCodeExpires()
    {
        return $this->passwordResetCodeExpires;
    }
}
