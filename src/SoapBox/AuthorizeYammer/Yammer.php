<?php namespace Soapbox\YammerOauth;

class Yammer {

	public function urlAuthorize()
	{
		return 'https://www.yammer.com/dialog/oauth';
	}

	public function urlAccessToken()
	{
		return 'https://www.yammer.com/oauth2/access_token.json';
	}

	public function urlUserDetails(AccessToken $token)
	{
		return 'https://www.yammer.com/api/v1/users/current.json?access_token=' . $token;
	}

	public function getAuthorizationUrl($options = array())
	{
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

	public function getAccessToken($params = array())
	{
		$defaultParams = array(
			'client_id'		=> $this->clientId,
			'client_secret'	=> $this->clientSecret,
			'redirect_uri'	=> $this->redirectUri
		);

		$ch = curl_init();
	}

}
