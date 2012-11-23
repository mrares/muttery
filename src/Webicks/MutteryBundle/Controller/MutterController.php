<?php

namespace Webicks\MutteryBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;


class MutterController extends Controller
{
    /**
     * @Route("/mutter/{id}")
     * @Template()
     */
    public function indexAction($id)
    {
    	$cache = $this->get('cache');
    	
    	//Getting My Friend list from cache, much faster than getting it from FB
    	if ($myFriends = $cache->get($this->getUser()->getFacebookId().'_friends')) {
    		$myFriends = json_decode($myFriends);
    	} else {
    		$FBu = $this->get('facebook');
    		$myFriends = $FBu->api('/me/friends');    		
    		$myFriends = $myFriends['data'];
    		$cache->set($this->getUser()->getFacebookId().'_friends', json_encode($myFriends), 600);
    	}
    	$FBu = $this->get('facebook');
    	$me = $FBu->api('/me');
    	
		// @todo: we need to assure that the mutter can be seen only by the invited users
    	$em = $this->getDoctrine ()->getEntityManager ();    	
    	$mutter = $em->find('\Webicks\MutteryBundle\Entity\Mutter',$id);    	
    	    		
    	return array(
    		'mutter'=>$mutter,
    		'me'=>$me,    		 
    		'myFriends'=>$myFriends,  
    	);
    }

}
