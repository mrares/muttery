<?php

namespace Webicks\MutteryBundle\Controller;

use Webicks\MutteryBundle\Entity\MutterData;

use Webicks\MutteryBundle\Entity\Invite;

use Webicks\MutteryBundle\Entity\Mutter;

use Symfony\Component\HttpFoundation\Response;

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
    		$myFriends = array_slice($myFriends, rand(0,count($myFriends)-9), 9);
    	}

        return array('myFriends'=>$myFriends);
    }

    /**
     * @Route("/saveMutter")
     */
    public function saveMutterAction() {
    	$request = $this->getRequest()->get('data');
    	$return = array();

    	if(empty($request['name'])) {
    		return new Response();
    	}

    	try {
	    	$em = $this->getDoctrine()->getEntityManager();
        	$mutter = new Mutter();
        	$mutter->setName($request['name']);
        	$mutter->setOwner($this->getUser());
        	$mutter->setDateActive(new \DateTime());

        	$mutter->setData(
        			new MutterData(
        					$request['type'],
        					$request['data']
        					)
        			);

        	foreach($request['invites'] as $invitee) {
        		$invite = new Invite();
        		$invite->setDestination($invitee);
        		$mutter->setInvite($invite);
        	}

    		$em->persist($mutter);
        	$em->flush();

        	$return = array(
        			"success"=>true,
        			"mutter_id"=>$mutter->getId()
        			);
    	} catch (Exception $e) {
    		$return = array(
    				"success"=>false,
    				"exception"=>true,
    				"message"=> $e->getMessage());
    	}

    	$response = new Response();
    	$response->setContent(json_encode($return));
    	return $response;
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
