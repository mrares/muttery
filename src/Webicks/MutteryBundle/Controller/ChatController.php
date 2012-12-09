<?php
namespace Webicks\MutteryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{

	/**
	 * Initialize chat
     * @Route("/chat", name="initChat")
     * @Template()
	 */
	public function initAction()
	{

	}

}
