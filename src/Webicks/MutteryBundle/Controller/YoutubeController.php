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
		$yt = $this->get('youtube');

		if($yt->isAuthenticated()) {
			//Already authenticated... show the upload form.
			return array('loggedIn'=>true);
		}

		return array(
				'dest'=>$yt->getAuthenticationUrl($this->generateUrl('iframeEscape')),
				'loggedIn'=>false
				);
	}

	/**
	 * Get out of the iframe
	 *
	 * @Route("/YTIframeEscape", name="iframeEscape")
	 */
	public function iframeEscapeAction()
	{
		return new response("<html><head>
				<script type='text/javascript'>
				opener.window.$(opener.window.document).trigger('YTLoggedin');
				window.close();
				</script>
				</head><body></body></html>");
	}

	/**
	 * Generate temporary video entry for upload and display upload form
	 *
	 * @Route("/ytUpload", name="videoUpload")
	 * @Template()
	 */
	public function uploadAction()
	{
		$vEntry = new \Zend_Gdata_YouTube_VideoEntry();

		$vEntry->setVideoTitle('Temporary Muttery video');
		$vEntry->setVideoCategory('People');
		$vEntry->setVideoDescription('');

		// Set unlisted
		$unlisted = new \Zend_Gdata_App_Extension_Element( 'yt:accessControl', 'yt',
				'http://gdata.youtube.com/schemas/2007', '' );
		$unlisted->setExtensionAttributes(array(
				array('namespaceUri' => '', 'name' => 'action', 'value' => 'list'),
				array('namespaceUri' => '', 'name' => 'permission', 'value' => 'denied')
		));
		$vEntry->setExtensionElements(array($unlisted));

		try {
    		$yt = $this->get('youtube')->getClient();
    		$uploadToken = $yt->getFormUploadToken($vEntry);
		} catch(\Exception $e) {
			//@todo: Manage exception here so that we don't upload bogus files...
			throw $e;
		}

		return array(
				'nextUrl'=>'http://muttery.rares.webicks.com'.$this->generateUrl('youtubeFinalize'),
				'postUrl'=>$uploadToken['url'],
				'tokenValue'=>$uploadToken['token']
				);
	}

	/**
	 * @Route("/ytFinalize", name="youtubeFinalize")
	 * @Template()
	 */
	public function ytFinalizeAction()
	{
		$status = $this->getRequest()->get('status');
		$id = $this->getRequest()->get('id',0);
		if($status!=200 || !$id) {
			return array('err'=>'Failed to upload!', 'id'=>false);
		}

		$yt = $this->get('youtube')->getClient();
		$vid = $yt->getFullVideoEntry($id);
		if(!$vid) {
			return array('err'=>'Failed to fetch video!', 'id'=>false);
		}

		return array('err'=>false, 'id'=>$id);
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
		$redirect = base64_decode($destination);

		$yt = $this->get('youtube');
		if(!$yt->processSingleToken($token)) {
    		$redirect = $yt->getAuthenticationUrl($redirect);
		}

		return $this->redirect($redirect);
	}

}
