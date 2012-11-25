<?php

namespace Webicks\MutteryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webicks\MutteryBundle\Entity\Invite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Webicks\MutteryBundle\Entity\InviteRepository")
 */
class Invite
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer $mutter_id
     *
     * @ORM\Column(name="mutter_id", type="integer")
     */
    private $mutter_id;

    /**
     * @var boolean $sent
     *
     * @ORM\Column(name="sent", type="boolean")
     */
    private $sent = 0;

    /**
     * @var boolean $actioned
     *
     * @ORM\Column(name="actioned", type="boolean")
     */
    private $actioned = 0;

    /**
     * @var integer $fb_id
     *
     * @ORM\Column(name="destination", type="string")
     */
    private $destination;

    /**
     * @var string $fb_name
     *
     * @ORM\Column(name="temp_name", type="string", length=255)
     */
    private $fb_name;
    
    /**
     * @ORM\ManyToOne(targetEntity="Mutter", inversedBy="invites")
     */
    private $mutter;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mutter_id
     *
     * @param Mutter $mutter
     * @return Invite
     */
    public function setMutter($mutter)
    {
        $this->mutter = $mutter;

        return $this;
    }

    /**
     * Get mutter_id
     *
     * @return \Webicks\MutteryBundle\Entity\Mutter
     */
    public function getMutter()
    {
        return $this->mutter;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     * @return Invite
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set actioned
     *
     * @param boolean $actioned
     * @return Invite
     */
    public function setActioned($actioned)
    {
        $this->actioned = $actioned;

        return $this;
    }

    /**
     * Get actioned
     *
     * @return boolean
     */
    public function getActioned()
    {
        return $this->actioned;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return Invite
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer
     */
    public function getDestination()
    {
        return $this->destination;
    }
}