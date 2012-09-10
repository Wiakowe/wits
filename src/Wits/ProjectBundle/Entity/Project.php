<?php

namespace Wits\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Wits\IssueBundle\Entity\Issue;
use Wits\IssueBundle\Entity\Version;
use Wits\UserBundle\Entity\User;


/**
 * @ORM\Table(name="project")
 * @ORM\Entity()
 */
class Project
{
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
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *          targetEntity="Wits\IssueBundle\Entity\Issue",
     *          mappedBy="project"
     * )
     */
    private $issues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *          targetEntity="Wits\ProjectBundle\Entity\Version",
     *          mappedBy="project"
     * )
     */
    private $versions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Wits\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="leader_id", referencedColumnName="id")
     *
     */
    protected $leader;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function gerVersions()
    {
        return $this->versions;
    }

    /**
     * @param \Wits\UserBundle\Entity\User $leader
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;
    }

    /**
     * @return \Wits\UserBundle\Entity\User
     */
    public function getLeader()
    {
        return $this->leader;
    }
}
