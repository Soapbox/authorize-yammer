<?php namespace SoapBox\AuthorizeYammer;

use Guzzle\Service\Client as GuzzleClient;

class Yammer {

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

	public function urlUserDetails($token) {
		return 'https://www.yammer.com/api/v1/users/current.json?access_token=' . $token;
	}

	public function getAuthorizationUrl($options = array()) {
		$this->state = md5(uniqid(rand(), true));

		$params = array(
			'client_id' => $this->clientId,
			'redirect_uri' => $this->redirectUri,
			'state' => $this->state,
			'scope' => is_array($this->scopes) ? implode($this->scopeSeparator, $this->scopes) : $this->scopes,
			'response_type' => isset($options['response_type']) ? $options['response_type'] : 'code',
			'approval_prompt' => 'auto'
		);

		return $this->urlAuthorize() . '?' . http_build_query($params, 0, $arg_separator, null);
	}

	public function getAccessToken($params = array()) {
		$defaultParams = array(
			'client_id'		=> $this->clientId,
			'client_secret'	=> $this->clientSecret,
			'code'			=> $code
		);

		$client = $this->httpClient;
		$client->setBaseUrl($this->urlAccessToken());
		$request = $client->post(null, null, $defaultParams)->send();
		$response = $request->getBody();

		// if (isset($result['error']) && ! empty($result['error'])) {
		//  // @codeCoverageIgnoreStart
		//  throw new IDPException($result);
		//  // @codeCoverageIgnoreEnd
		// }

		$response = json_decode($response);
		return $response;
	}

	public function getUserDetails($token) {
		$url = $this->urlUserDetails($token);

		$client = $this->httpClient;
		$client->setBaseUrl($url);
		$request = $client->get()->send();
		$response = $request->getBody();

		return json_decode($response);
	}
}
