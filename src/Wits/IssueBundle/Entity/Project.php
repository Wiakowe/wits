<?php

namespace Wits\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Wits\IssueBundle\Entity\Issue;
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
     *          targetEntity="Issue",
     *          mappedBy="project"
     * )
     */
    private $issues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *          targetEntity="Version",
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
     * @param array|\Doctrine\Common\Collections\Collection $issues
     */
    public function setIssues($issues)
    {
        $this->setDoctrineCollectionElement('issues', $issues);
    }

    /**
     * @param Issue $issue
     */
    public function addIssue(Issue $issue)
    {
        $this->issues->add($issue);
    }

    /**
     * @param Issue $issue
     */
    public function removeIssue(Issue $issue)
    {
        $this->products->removeElement($issue);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @param array|\Doctrine\Common\Collections\Collection $versions
     */
    public function setVersions($versions)
    {
        $this->setDoctrineCollectionElement('versions', $versions);
    }

    /**
     * @param Version $version
     */
    public function addVersion(Version $version)
    {
        $this->versions->add($version);
    }

    /**
     * @param Version $version
     */
    public function removeVersion(Version $version)
    {
        $this->versions->removeElement($version);
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
