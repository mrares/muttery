<?php

namespace Webicks\MutteryBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Webicks\MutteryBundle\Entity\Invite;
use Webicks\MutteryBundle\Entity\Mutter;
use Webicks\MutteryBundle\Entity\MutterData;

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
    	$em = $this->getDoctrine()->getEntityManager();
		$mutter = $em->find('\Webicks\MutteryBundle\Entity\Mutter',$id);

    	return array(
    		'mutter'=>$mutter,
    		'me'=>$me,
    		'myFriends'=>$myFriends,
    	);
    }

    /**
     * Save a mutter
     *
     * @Route("/saveMutter", name="_secured_save_mutter")
     * @Secure(roles="ROLE_FACEBOOK")
     */
    public function saveMutterAction() {
    	$request = $this->getRequest()->get('data');
    	$return = array();

    	//Redirect to HP if the request is invalid
    	if(empty($request['name'])) {
    		return $this->redirect($this->generateUrl('HomePage'));
    	}

    	try {
    		$em = $this->getDoctrine()->getEntityManager();
    		$mutter = new Mutter();
    		$mutter->setName($request['name']);
    		$mutter->setDateActive(new \DateTime(date('Y-m-d H:i',strtotime($request['from']))));
    		$mutter->setDateEnd(new \DateTime(date('Y-m-d H:i',strtotime($request['until']))));
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

    		if($request['type'] == 'youtube') {
    			$this->postProcessVideo($mutter);
    		}

    		$return = array(
    				"success"=>true,
    				"mutter_id"=>$mutter->getId()
    		);
    	} catch (\Exception $e) {
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
     * @param Mutter $mutter
     */
    private function postProcessVideo($mutter) {
    	$md = $mutter->getData();
    	$data = $md->getData();
    }

}
