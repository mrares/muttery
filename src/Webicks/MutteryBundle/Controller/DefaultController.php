<?php

namespace Webicks\MutteryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="HomePage")
     * @Template()
     */
    public function indexAction()
    {
    	$myFriends = false;
    	$mutters = false;
    	$invites = false;
    	$user = false;
    	$multiple = 0;

    	$logger = $this->get('logger');

    	$user = $this->getUser();
    	if($user && $user->hasRole('ROLE_FACEBOOK')) {
    		/*
    		 * Getting memcache service to use the caching layer
    		 * @var Memcached
    		 */
    		$cache = $this->get('cache');

    		//Getting My Friend list from cache, much faster than getting it from FB
    		$myFriends = $cache->get($this->getUser()->getFacebookId().'_friends');
    		if ($myFriends) {
    			$myFriends = json_decode($myFriends);
    		} else {
    			$FBu = $this->get('facebook');
    			$myFriends = $FBu->api('/me/friends');
    			$myFriends = $myFriends['data'];
    			$cache->set($this->getUser()->getFacebookId().'_friends', json_encode($myFriends), 600);
    		}
    		$em = $this->getDoctrine()->getEntityManager();
    		$mutters = $user->getMutters();

    		// @todo: we need to retrieve only the active mutters (through a function in doctrine or condition is the below loop)
    		foreach ($mutters as $mutter)
    		{
    			if ($mutter->getDateActive() > date('Y-m-y H:m:s'));
    			{

    			}
    		}

    		$invites = $em->getRepository('\Webicks\MutteryBundle\Entity\Invite')->findBy(array(
    				'destination' => $user->getFacebookId(),
    		));

    		if (count($mutters) > 1 || count($invites) > 1 || count($mutters)==1 && count($invites)==1)
    		{
    			$multiple = 1;
    		}

    		$myFriends = array_slice($myFriends, rand(0,count($myFriends)-9), 9);
    	}

        return array(
        	'myFriends'=>$myFriends,
        	'mutters'=>$mutters,
        	'invites'=>$invites,
        	'user'=>$user,
        	'multiple'=>$multiple,
        );
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
