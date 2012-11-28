<?php

namespace Webicks\MutteryBundle\Service\YouTube;

class YouTube {

	private $session = null;
	private $developerKey = null;
	private $sessionToken = null;
	private $router = null;

	public function __construct($session, $router, $developerKey) {
		$this->session = $session;
		$this->sessionToken = $this->session->get('sessionToken');
		$this->router = $router;
		$this->developerKey = $developerKey;
	}

	public function isAuthenticated() {
		return ($this->sessionToken != false);
	}

	public function getClient() {
		if(!$this->sessionToken) {
			throw new \Exception("Not authorized");
		}

		$httpClient = \Zend_Gdata_AuthSub::getHttpClient($this->sessionToken);
		$yt = new \Zend_Gdata_YouTube($httpClient, 'Muttery', null, $this->developerKey);
		$yt->setMajorProtocolVersion(2);
		$yt->setGzipEnabled(true);
		return $yt;
	}

	/**
	 * Get youtube authorization request URL
	 * @return string
	 * @param string Redirect destination
	 */
	public function getAuthenticationUrl($redirect = '/')
	{
		$destination = 'http://'.$_SERVER['HTTP_HOST'];
		$destination .= $this->router->generate('YToken',
				array('destination'=>base64_encode($redirect))
		);

		$scope = 'http://gdata.youtube.com';

		return \Zend_Gdata_AuthSub::getAuthSubTokenUri($destination, $scope, false, true);
	}

	/**
	 * Process youtube single-use token and turn it into a session token
	 *
	 * @param string $token
	 * @return boolean
	 */
	public function processSingleToken($token)
	{
		if(!$token) {
			return false;
		}
// 		try {
			$this->sessionToken = \Zend_Gdata_AuthSub::getAuthSubSessionToken($token);
// 		} catch(\Exception $e) {
// 			return false;
// 		}

		$this->session->set('sessionToken', $this->sessionToken);
	}

}