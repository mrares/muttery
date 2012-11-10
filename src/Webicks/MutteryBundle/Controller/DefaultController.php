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
    	var_dump( $this->get('security.context')->getToken()->getUser() );
    	var_dump( $this->getUser() );
		$user = $this->get('security.context')->getToken()->getUser();

    	if(is_object($user) && $user->getRole('ROLE_FB')) {
        	$FBu = $this->get('fb.user');
        	if($FBu->getMe()) {
        		$myFriends = $FBu->getFriends();
        	}
    	}
//     	$friend = $myFriends['data'][0];
//     	var_dump($friend);

//     	var_dump($FBu->fb()->api("/me/picture"));
//     	$userkeys = array_keys($user) ;

//     	foreach($userkeys as $key) {
//     		var_dump($key);
//     	}

//     	var_dump($this->get('my.facebook.user')->getUser('corina.mosescu'));

//     	var_dump($this->get('my.facebook.user')->getFriends());
//     	$securityContext = $container->get('security.context');
//         $token = $securityContext->getToken();
//         $user = $token->getUser();
//     	var_dump($user);
        return array('pending'=>array('rares','corina'), 'myFriends'=>$myFriends['data']);
    }
}
