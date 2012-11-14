<?php

namespace Webicks\MutteryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
    	$myFriends = false;

    	$logger = $this->get('logger');

    	if($this->getUser() && $this->getUser()->hasRole('ROLE_FACEBOOK')) {
    		/*
    		 * @var Memcached
    		 */
    		$cache = $this->get('cache');
    		if ($myFriends = $cache->get($this->getUser()->getFacebookId().'_friends')) {
    			$logger->info("Cache hit: friends");
    			$myFriends = json_decode($myFriends);
    		} else {
    			$logger->info("Cache miss: friends");
    			$FBu = $this->get('facebook');
    			$myFriends = $FBu->api('/me/friends');
    			$myFriends = $myFriends['data'];
    			$cache->set($this->getUser()->getFacebookId().'_friends', json_encode($myFriends), 600);
    		}
    		$myFriends = array_slice($myFriends, 0, 9);
    	}

        return array('myFriends'=>$myFriends);
    }

    /**
     * @Route("/loginFB")
     */
    public function loginAction()
    {
    	$fb = $this->get('facebook');
    	$fbdata = $fb->api('/me');
    	if(!empty($fbdata)) {
        	$this->get('facebook.user')->loadUserByUsername($fbdata['id']);
    	}
    	return $this->redirect('/');
    }
}
