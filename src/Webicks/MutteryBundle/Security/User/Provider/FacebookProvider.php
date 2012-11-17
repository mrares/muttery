<?php
namespace Webicks\MutteryBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use \BaseFacebook;
use \FacebookApiException;

class FacebookProvider implements UserProviderInterface
{
    /**
     * @var \Facebook
     */
    protected $facebook;
    protected $userManager;
    protected $validator;
    protected $cache;

    public function __construct(BaseFacebook $facebook, $userManager, $validator, $cache)
    {
        $this->facebook = $facebook;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->cache = $cache;
    }

    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }

    public function findUserByFbId($fbId)
    {
        return $this->userManager->findUserBy(array('facebookId' => $fbId));
    }

    public function loadUserByUsername($username)
    {
    	if ($fbdata = $this->cache->get('fbu_'.$username)) {
    		$fbdata = json_decode($fbdata, true);
    	} else {
            try {
                $fbdata = $this->facebook->api('/me');
                $this->cache->set('fbu_'.$username, json_encode($fbdata), 60);
            } catch (FacebookApiException $e) {
            	throw new UsernameNotFoundException('The user is not authenticated on facebook');
                $fbdata = null;
            }
    	}

        $user = $this->findUserByFbId($username);

        if (!empty($fbdata)) {
            if (empty($user)) {
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                $user->setPassword('');
            }

            // TODO use http://developers.facebook.com/docs/api/realtime
            $user->setFBData($fbdata);

            if (count($this->validator->validate($user, 'Facebook'))) {
                // TODO: the user was found obviously, but doesnt match our expectations, do something smart
                throw new UsernameNotFoundException('The facebook user could not be stored');
            }
            $this->userManager->updateUser($user);
        } else {
            throw new UsernameNotFoundException('The user is not authenticated on facebook');
        }

        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on facebook');
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user)) || !$user->getFacebookId()) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getFacebookId());
    }
}