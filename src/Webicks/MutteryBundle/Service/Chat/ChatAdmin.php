<?php

namespace Webicks\MutteryBundle\Service\Chat;

class ChatAdmin {

	private $session, $router;

    public function __construct($session, $router) {
        $this->session = $session;
        $this->router = $router;
    }

    public function pushLogin($params){
    	$client = new \Zend_Http_Client();
    	$client->setUri('http://127.0.0.1:8088/push');
    	$client->setRawData(json_encode($params));
    	$response = $client->request(\Zend_Http_Client::POST);
    	
    	return $response;
    }

}

