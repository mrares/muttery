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

class MutterController extends Controller {
	/**
	 * @Route("/mutter/{id}")
	 * @Template()
	 */
	public function indexAction($id) {
		$cache = $this->get ( 'cache' );

		// invited users
		$em = $this->getDoctrine ()->getEntityManager ();
		$mutter = $em->find ( '\Webicks\MutteryBundle\Entity\Mutter', $id );

		$user = $this->getUser ();
		if (! $user || ! $user->hasRole ( 'ROLE_FACEBOOK' )) {
			return $this->redirect('/');
		}

		// Getting My Friend list from cache, much faster than getting it from
		// FB
		if ($myFriends = $cache->get ( $this->getUser ()->getFacebookId () . '_friends' )) {
			$myFriends = json_decode ( $myFriends );
		} else {
			$FBu = $this->get ( 'facebook' );
			$myFriends = $FBu->api ( '/me/friends' );
			$myFriends = $myFriends ['data'];
			$cache->set ( $this->getUser ()->getFacebookId () . '_friends', json_encode ( $myFriends ), 600 );
		}

		$me = $this->get ( 'facebook' )->api ( '/me' );

		return array(
			'mutter' => $mutter,
			'me' => $me,
			'myFriends' => $myFriends
		);
	}

	/**
	 * Save a mutter
	 *
	 * @Route("/saveMutter", name="_secured_save_mutter")
	 * @Secure(roles="ROLE_FACEBOOK")
	 */
	public function saveMutterAction() {
		$request = $this->getRequest ()->get ( 'data' );
		$return = array();

		// Redirect to HP if the request is invalid
		if (empty ( $request ['name'] )) {
			return $this->redirect ( $this->generateUrl ( 'HomePage' ) );
		}

		try {
			$em = $this->getDoctrine ()->getEntityManager ();
			$mutter = new Mutter ();
			$mutter->setName ( $request ['name'] );
			$mutter->setDateActive ( new \DateTime ( date ( 'Y-m-d H:i', strtotime ( $request ['from'] ) ) ) );
			$mutter->setDateEnd ( new \DateTime ( date ( 'Y-m-d H:i', strtotime ( $request ['until'] ) ) ) );
			$mutter->setOwner ( $this->getUser () );
			$mutter->setDateActive ( new \DateTime () );

			$mutter->setData ( new MutterData ( $request ['type'], $request ['data'] ) );

			foreach ( $request ['invites'] as $invitee ) {
				$invite = new Invite ();
				$invite->setDestination ( $invitee );
				$mutter->setInvite ( $invite );
			}
			if ($request ['type'] == 'youtube') {
				$this->postProcessVideo ( $mutter );
			}

			$em->persist ( $mutter );
			$em->flush ();

			$return = array(
				"success" => true,
				"mutter_id" => $mutter->getId ()
			);
		} catch ( \Exception $e ) {
			$return = array(
				"success" => false,
				"exception" => true,
				"message" => $e->getMessage ()
			);
		}

		$response = new Response ();
		$response->setContent ( json_encode ( $return ) );
		return $response;
	}

	/**
	 *
	 * @param Mutter $mutter
	 */
	private function postProcessVideo($mutter) {
		$videoID = $mutter->getData ()->getData ();
		$yt = $this->get ( 'youtube' )->getClient ();
		$videoEntry = $yt->getFullVideoEntry ( $videoID );
		$videoEntry->setVideoTitle ( "Muttery: " . $mutter->getName () );
		$flashPlayer = $videoEntry->getFlashPlayerUrl ();
		if ($flashPlayer) {
			$mutter->getData ()->setData ( $flashPlayer );
		}
		$yt->updateEntry ( $videoEntry );
	}

	/**
	 * Execute mutter action
	 *
	 * @Route("/mutterAction/{mutterId}", name="mutterAction")
	 * @template()
	 *
	 * @param unknown_type $mutterId
	 */
	public function showAction($mutterId) {
		$em = $this->getDoctrine ()->getEntityManager ();
		$mutter = $em->find ( '\Webicks\MutteryBundle\Entity\Mutter', $mutterId );

		if ($mutter->getDateActive () > new \DateTime ()) {
			return $this->redirect ( '/' );
		}

		$mutterData = $mutter->getData ();
		$type = $mutterData->getType ();
		$data = $mutterData->getData ();

		if ($type == MutterData::TYPE_YOUTUBE) {
			try {
				$yt = $this->get ( 'youtube' )->getClient ( false );
				$video = $yt->getVideoEntry ( $mutter->getData ()->getData () );
				$data = $video->getFlashPlayerUrl ();
			} catch ( \Exception $e ) {
				$type = MutterData::TYPE_MESSAGE;
				$data = "Video no longer available";
			}
		}

		return array(
			'type' => $type,
			'data' => $data
		);
	}
}
