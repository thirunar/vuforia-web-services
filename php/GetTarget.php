<?php

require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';

// See the Vuforia Web Services Developer API Specification - https://developer.vuforia.com/resources/dev-guide/retrieving-target-cloud-database
// The GetTarget sample demonstrates how to query a single target by target id.
class GetTarget{

	//Server Keys
	private $access_key 	= "cd6b724d3b520cc072ddfb826e6cf7ea62835179";
	private $secret_key 	= "3d2387ec9d4fcbfdd8a565f9f002fc5427bd9efc";
	
	private $targetId 	= "d4c132cea4a64a66bb6fa0c288958d8b";
	private $url 		= "https://vws.vuforia.com";
	private $requestPath = "/targets/";// . $targetId;
	private $request;
	
	function GetTarget(){

		$this->requestPath = $this->requestPath . $this->targetId;
		
		$this->execGetTarget();
	}
	
	private function execGetTarget(){
		
		$this->request = new HTTP_Request2();
		$this->request->setMethod( HTTP_Request2::METHOD_GET );
		
		$this->request->setConfig(array(
				'ssl_verify_peer' => false
		));
		
		$this->request->setURL( $this->url . $this->requestPath );
		
		// Define the Date and Authentication headers
		$this->setHeaders();
		
		
		try {
		
			$response = $this->request->send();
		
			if (200 == $response->getStatus()) {
				echo $response->getBody();
			} else {
				echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
						$response->getReasonPhrase(). ' ' . $response->getBody();
			}
		} catch (HTTP_Request2_Exception $e) {
			echo 'Error: ' . $e->getMessage();
		}
		
		
	}
	
	private function setHeaders(){
		$sb = 	new SignatureBuilder();
		$date = new DateTime("now", new DateTimeZone("GMT"));

		// Define the Date field using the proper GMT format
		$this->request->setHeader('Date', $date->format("D, d M Y H:i:s") . " GMT" );
		// Generate the Auth field value by concatenating the public server access key w/ the private query signature for this request
		$this->request->setHeader("Authorization" , "VWS " . $this->access_key . ":" . $sb->tmsSignature( $this->request , $this->secret_key ));

	}
}

?>