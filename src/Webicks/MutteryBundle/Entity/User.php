<?php

namespace Webicks\MutteryBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Webicks\MutteryBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Webicks\MutteryBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255)
     */
    protected $facebookId;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Mutter", mappedBy="owner")
     */
    private $mutters;       

    public function serialize()
    {
    	return serialize(array($this->facebookId, parent::serialize()));
    }

    public function unserialize($data)
    {
    	list($this->facebookId, $parentData) = unserialize($data);
    	parent::unserialize($parentData);
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
    	return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
    	$this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
    	return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
    	$this->lastname = $lastname;
    }

    /**
     * Get the full name of the user (first + last name)
     * @return string
     */
    public function getFullName()
    {
    	return $this->getFirstName() . ' ' . $this->getLastname();
    }

    /**
     * @param string $facebookId
     * @return void
     */
    public function setFacebookId($facebookId)
    {
    	$this->facebookId = $facebookId;
    	$this->setUsername($facebookId);
    	$this->salt = '';
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
    	return $this->facebookId;
    }
    
    /**
     * Get mutter_id
     *
     * @return \Webicks\MutteryBundle\Entity\Mutter
     */
    public function getMutters()
    {
    	return $this->mutters;
    }

    /**
     * @param Array
     */
    public function setFBData($fbdata)
    {
    	if (isset($fbdata['id'])) {
    		$this->setFacebookId($fbdata['id']);
    		$this->addRole('ROLE_FACEBOOK');
    	}
    	if (isset($fbdata['first_name'])) {
    		$this->setFirstname($fbdata['first_name']);
    	}
    	if (isset($fbdata['last_name'])) {
    		$this->setLastname($fbdata['last_name']);
    	}
    	if (isset($fbdata['email'])) {
    		$this->setEmail($fbdata['email']);
    	}

    	return $this;
    }

}
