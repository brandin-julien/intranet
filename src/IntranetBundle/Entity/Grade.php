<?php

namespace IntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grade
 *
 * @ORM\Table(name="grade")
 * @ORM\Entity(repositoryClass="IntranetBundle\Repository\GradeRepository")
 */
class Grade
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="grade", type="integer")
     */
    private $grade;

    /**
     * @var int
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="matter")
     * @ORM\JoinColumn(nullable=false)
     */
    private $matter;

    /**
     * @ORM\OneToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set grade
     *
     * @param integer $grade
     *
     * @return Grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return int
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Grade
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set matter
     *
     * @param \IntranetBundle\Entity\matter $matter
     *
     * @return Grade
     */
    public function setMatter(\IntranetBundle\Entity\matter $matter)
    {
        $this->matter = $matter;

        return $this;
    }

    /**
     * Get matter
     *
     * @return \IntranetBundle\Entity\matter
     */
    public function getMatter()
    {
        return $this->matter;
    }

    /**
     * Set user
     *
     * @param \IntranetBundle\Entity\User $user
     *
     * @return Grade
     */
    public function setUser(\IntranetBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \IntranetBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
