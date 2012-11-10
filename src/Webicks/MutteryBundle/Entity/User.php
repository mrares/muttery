<?php

namespace Webicks\MutteryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webicks\MutteryBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Webicks\MutteryBundle\Entity\UserRepository")
 */
class User
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;


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
     * @return User
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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }



    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255)
     */
    protected $facebookId;

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
     * @param Array
     */
    public function setFBData($fbdata)
    {
    	if (isset($fbdata['id'])) {
    		$this->setFacebookId($fbdata['id']);
//     		$this->addRole('ROLE_FACEBOOK');
    	}
    	if (isset($fbdata['first_name'])) {
//     		$this->setFirstname($fbdata['first_name']);
    	}
    	if (isset($fbdata['last_name'])) {
//     		$this->setLastname($fbdata['last_name']);
    	}
    	if (isset($fbdata['email'])) {
    		$this->setEmail($fbdata['email']);
    	}
    }
}
