<?php

namespace Webicks\MutteryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webicks\MutteryBundle\Entity\Mutter
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Webicks\MutteryBundle\Entity\MutterRepository")
 */
class Mutter
{

	public function __construct() {
		$this->date_added = new \DateTime();
	}

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime $date_added
     *
     * @ORM\Column(name="date_added", type="datetime")
     */
    private $date_added;

    /**
     * @var \DateTime $date_active
     *
     * @ORM\Column(name="date_active", type="datetime", nullable = true)
     */
    private $date_active = null;

    /**
     * @var \DateTime $date_end
     *
     * @ORM\Column(name="date_end", type="datetime", nullable = true)
     */
    private $date_end = null;

    /**
     * @var integer $done
     *
     * @ORM\Column(name="done", type="integer")
     */
    private $done = 0;

    /**
     *
     * @ORM\OneToMany(targetEntity="Invite", mappedBy="mutter", cascade={"persist", "remove"})
     */
    private $invites = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mutters")
     */
    private $owner;

    /**
     * @ORM\OneToOne(targetEntity="MutterData", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $data = null;

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
     * Set name
     *
     * @param string $name
     * @return Mutter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set date_added
     *
     * @param \DateTime $dateAdded
     * @return Mutter
     */
    public function setDateAdded($dateAdded)
    {
        $this->date_added = $dateAdded;

        return $this;
    }

    /**
     * Get date_added
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
    	return $this->date_added;
    }

    /**
     * Set date_active
     *
     * @param \DateTime $dateActive
     * @return Mutter
     */
    public function setDateActive($dateActive)
    {
        $this->date_active = $dateActive;

        return $this;
    }

    /**
     * Get date_active
     *
     * @return \DateTime
     */
    public function getDateActive()
    {
        return $this->date_active;
    }

    /**
     * Set date_end
     *
     * @param \DateTime $dateEnd
     * @return Mutter
     */
    public function setDateEnd($dateEnd)
    {
        $this->date_end = $dateEnd;

        return $this;
    }

    /**
     * Get date_end
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set done
     *
     * @param integer $done
     * @return \Webicks\MutteryBundle\Entity\Mutter
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return integer
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * Get owner
     *
     * @return \Webicks\MutteryBundle\Entity\User
     */
    public function getOwner()
    {
    	return $this->owner;
    }

    /**
     * Set owner
     *
     * @param User $owner_id
     * @return \Webicks\MutteryBundle\Entity\Mutter
     */
    public function setOwner($owner)
    {
    	$this->owner = $owner;

    	return $this;
    }

    /**
     * Get data
     *
     * @return MutterData
     */
	public function getData() {
		return $this->data;
	}

    /**
     * Set data
     *
     * @param MutterData $owner_id
     * @return \Webicks\MutteryBundle\Entity\Mutter
     */
	public function setData($data) {
		$this->data = $data;
		$this->data->setMutter($this);

		return $this;
	}

	public function getInvites() {
		return $this->invites;
	}

	public function setInvite($invite) {
		if ($this->invites == null ) {
			$this->invites = new ArrayCollection();
		}

		$this->invites[] = $invite;
		$invite->setMutter($this);

		return $this;
	}

}