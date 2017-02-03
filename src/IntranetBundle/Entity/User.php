<?php

namespace IntranetBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="IntranetBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="matter", mappedBy="users")
     */
    private $matters;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Add matter
     *
     * @param \IntranetBundle\Entity\matter $matter
     *
     * @return User
     */
    public function addMatter(\IntranetBundle\Entity\matter $matter)
    {
        $this->matters[] = $matter;

        return $this;
    }

    /**
     * Remove matter
     *
     * @param \IntranetBundle\Entity\matter $matter
     */
    public function removeMatter(\IntranetBundle\Entity\matter $matter)
    {
        $this->matters->removeElement($matter);
    }

    /**
     * Get matters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatters()
    {
        return $this->matters;
    }
}
