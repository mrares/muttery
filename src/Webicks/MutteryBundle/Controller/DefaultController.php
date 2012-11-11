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

    	if($this->getUser() && $this->getUser()->hasRole('ROLE_FACEBOOK')) {
        	$FBu = $this->get('facebook');
    	    $myFriends = $FBu->api('/me/friends');
    	    $myFriends = array_slice($myFriends['data'], 0, 9);
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
        	$this->get('my.fb.user')->loadUserByUsername($fbdata['id']);
    	}
    	return $this->redirect('/');
    }
}
