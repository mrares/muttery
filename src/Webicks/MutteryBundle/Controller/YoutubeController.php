<?php
namespace Webicks\MutteryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class YoutubeController extends Controller
{

	/**
	 * Initialize Youtube upload request
     * @Route("/youtube", name="initYoutube")
     * @Template()
	 */
	public function initAction()
	{
		if($authToken = $this->get('session')->get('sessionToken')) {
			//Already authenticated... show the upload form.
			return array('loggedIn'=>true);
		}

		$upload = $this->generateUrl('videoUpload');

		$iframe= $this->generateUrl('iframeEscape',
				array('js'=>base64_encode("
						opener.window.$(opener.window.document).trigger('YTLoggedin');
						window.close();
						"))
				);

		$iframeDest = $this->getYTAuthUrl($iframe);

		return array('dest'=>$iframeDest,'loggedIn'=>false);
	}


	/**
	 * Get out of the iframe
	 *
	 * @Route("/YTIframeEscape/{js}/", name="iframeEscape")
	 */
	public function iframeEscapeAction($js)
	{
		$js = base64_decode($js);

		$res = new response();
		$res->setContent("
				<html>
				<head>
				<script type='text/javascript'>
				".$js."
				</script>
				</head>
				<body>
				</body>
				</html>
				");
		return $res;
	}

	/**
	 * Display upload form
	 *
	 * @Route("/ytUpload", name="videoUpload")
	 * @Template()
	 */
	public function uploadAction()
	{
		$uploadToken = $this->createVideoEntry();

		return array(
				'nextUrl'=>'http://muttery.rares.webicks.com/',
				'postUrl'=>$uploadToken['url'],
				'tokenValue'=>$uploadToken['token']
				);
	}

	/**
	 * Process YouTube authorization token and obtain a permanent session token
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @Route("/authYToken/{destination}/", name="YToken")
	 */
	public function processAuthorizationTokenAction($destination)
	{
		$token = $this->getRequest()->get('token');

		var_dump($token);

		$session = $this->get('session');
		$redirectDestination = base64_decode($destination);
		var_dump($redirectDestination);

		if(!$token) {
			$redirectDestination = $this->getYTAuthUrl($redirectDestination);
		} elseif(!$session->get('sessionToken')) {
			$sessionToken = \Zend_Gdata_AuthSub::getAuthSubSessionToken($token);
			var_dump($sessionToken);
			$session->set('sessionToken', $sessionToken);
		}

		return $this->redirect($redirectDestination);
	}

	public function createVideoEntry()
	{
		$sessionToken = $this->get('session')->get('sessionToken');

		if(!$sessionToken) {
			return false;
		}

		$httpClient = \Zend_Gdata_AuthSub::getHttpClient($sessionToken);

		$yt = new \Zend_Gdata_YouTube($httpClient, 'Muttery', null, 'AI39si7kawwsk2nlwc3ZF52kASmkTJLP16XISgVayh6lwI-tlJA_leickIE-ujf8ggjB4xFl3C48ERvOelsXav5XmHyaiGM5gg');
		$yt->setMajorProtocolVersion(2);
		$yt->setGzipEnabled(true);

		$vEntry = new \Zend_Gdata_YouTube_VideoEntry();

		$vEntry->setVideoTitle('Test upload vid');
		$vEntry->setVideoCategory('People');
		$vEntry->setVideoDescription('My test vid upload');

		// Set unlisted
		$unlisted = new \Zend_Gdata_App_Extension_Element( 'yt:accessControl', 'yt',
				'http://gdata.youtube.com/schemas/2007', '' );
		$unlisted->setExtensionAttributes(array(
				array('namespaceUri' => '', 'name' => 'action', 'value' => 'list'),
				array('namespaceUri' => '', 'name' => 'permission', 'value' => 'denied')
		));
		$vEntry->setExtensionElements(array($unlisted));

		return $yt->getFormUploadToken($vEntry);
	}

	/**
	 * Get youtube authorization request URL
	 * @return string
	 * @param string Redirect destination
	 */
	public function getYTAuthUrl($redirect = '/')
	{
		$destination = 'http://'.$_SERVER['HTTP_HOST'];
		$destination .= $this->generateUrl('YToken',
				array('destination'=>base64_encode($redirect))
				);

		$scope = 'http://gdata.youtube.com';

		return \Zend_Gdata_AuthSub::getAuthSubTokenUri($destination, $scope, false, true);
	}

}
