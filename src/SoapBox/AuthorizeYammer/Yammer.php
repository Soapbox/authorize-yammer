<?php namespace SoapBox\AuthorizeYammer;

use Guzzle\Service\Client as GuzzleClient;

class Yammer {

	public $clientId = '';

	public $clientSecret = '';

	public $redirectUri = '';

	protected $httpClient;

	public function __construct($options = array()) {

		foreach ($options as $option => $value) {
			if (isset($this->{$option})) {
				$this->{$option} = $value;
			}
		}

		$this->httpClient = new GuzzleClient;
	}

	public function urlAuthorize() {
		return 'https://www.yammer.com/dialog/oauth';
	}

	public function urlAccessToken() {
		return 'https://www.yammer.com/oauth2/access_token.json';
	}

	public function getAuthorizationUrl($options = array()) {
		$this->state = md5(uniqid(rand(), true));

		$params = array(
			'client_id' => $this->clientId,
			'redirect_uri' => $this->redirectUri,
			'state' => $this->state,
			'response_type' => isset($options['response_type']) ? $options['response_type'] : 'code',
			'approval_prompt' => 'auto'
		);

		return $this->urlAuthorize() . '?' . http_build_query($params, 0, '&', null);
	}

	public function getAccessToken($params = array()) {
		$defaultParams = array(
			'client_id'		=> $this->clientId,
			'client_secret'	=> $this->clientSecret,
			'code'			=> $params['code']
		);

		$client = $this->httpClient;
		$client->setBaseUrl($this->urlAccessToken());
		$request = $client->post(null, null, $defaultParams)->send();
		$response = $request->getBody();

		$response = json_decode($response);
		return $response;
	}
}
