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

    	$yt = $this->get('youtube');

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

    		$mutters = $em->getRepository('Webicks\MutteryBundle\Entity\Mutter')->getActiveMutters($user);
    		$invites = $em->getRepository('Webicks\MutteryBundle\Entity\Mutter')->getActiveInvites($user->getFacebookId());

    		$results = count($mutters)+count($invites);

    		if ($results==1)
    		{
    			if (count($mutters)==0)
    			{
    				$id = $invites[0]->getId();
    			}	else	{
    				$id = $mutters[0]->getId();
    			}
    			return $this->redirect('mutter/'.$id);
    		}
    		$myFriends = array_slice($myFriends, rand(0,count($myFriends)-9), 9);
    	}

        return array(
        	'myFriends'=>$myFriends,
        	'mutters'=>$mutters,
        	'invites'=>$invites,
        	'user'=>$user,
        	'results'=>$results,
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
