<?php

namespace Webicks\MutteryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MutterData
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Webicks\MutteryBundle\Entity\MutterDataRepository")
 */
class MutterData
{

	/**
	 * Create a MutterData record pre-populated with data
	 *
	 * @param string $type
	 * @param string $data
	 */
	public function __construct($type = null, $data = null) {
		if($type && $data) {
			$this->type = $type;
			$this->data = $data;
		}
	}

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="string", length=32)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;

    /**
     * @ORM\OneToOne(targetEntity="Mutter", inversedBy="data")
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
     * Set type
     *
     * @param integer $type
     * @return MutterData
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return MutterData
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Set Mutter relationship
     *
     * @param \Webicks\MutteryBundle\Entity\Mutter $mutter
     * @return \Webicks\MutteryBundle\Entity\MutterData
     */
    public function setMutter($mutter)
    {
    	$this->mutter = $mutter;

    	return $this;
    }
}
