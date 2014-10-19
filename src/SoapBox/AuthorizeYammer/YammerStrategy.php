<?php namespace SoapBox\AuthorizeYammer;

use SoapBox\Authorize\Helpers;
use SoapBox\Authorize\User;
use SoapBox\Authorize\Strategies\SingleSignOnStrategy;
use SoapBox\Authorize\Exceptions\MissingArgumentsException;
use SoapBox\Authorize\Exceptions\AuthenticationException;

class YammerStrategy extends SingleSignOnStrategy {

	private $yammer;

	public static $store = null;
	public static $load = null;

	public function __construct($parameters = array(), $store = null, $load = null) {
		if( !isset($parameters['api_key']) ||
			!isset($parameters['api_secret']) ||
			!isset($parameters['redirect_url']) ) {
			throw new MissingArgumentsException(
				'Required parameters api_key, api_secret, or redirect_url are missing'
			);
		}

		$this->yammer = new Yammer(array(
			'clientId'		=>	$parameters['api_key'],
			'clientSecret'	=>	$parameters['api_secret'],
			'redirectUri'	=>	$parameters['redirect_url']
		));

		if ($store != null && $load != null) {
			YammerStrategy::$store = $store;
			YammerStrategy::$load = $load;
		} else {
			session_start();
			YammerStrategy::$store = function($key, $value) {
				$_SESSION[$key] = $value;
			};
			YammerStrategy::$load = function($key) {
				return $_SESSION[$key];
			};
		}
	}

	public function login($parameters = array()) {
		return $this->endPoint($parameters);
	}

	public function getUser($parameters = array()) {

		// Try to get the access token using auth code
		$response =  $this->yammer->getAccessToken([
			'code' => $parameters['code']
		]);

		$user = new User;
		$user->id = $response->user->id;
		$user->email = $response->user->contact->email_addresses[0]->address;
		$user->accessToken = json_encode($response->access_token);
		$user->firstname = $response->user->first_name;
		$user->lastname = $response->user->last_name;

		return $user;
	}

	public function endPoint($parameters = array()) {

		if ( !isset($_GET['code'])) {
			Helpers::redirect($this->yammer->getAuthorizationUrl());
		}

		return $this->getUser(['code'=>$_GET['code']]);
	}
}
