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
			'clientId'		=>	$settings['api_key'],
			'clientSecret'	=>	$settings['api_secret'],
			'redirectUri'	=>	$settings['redirect_url']
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
		if (!isset($parameters['accessToken'])) {
			throw new AuthenticationException();
		}

		$accessToken = $parameters['accessToken'];
		$response = $this->yammer->getUserDetails($accessToken);

		$user = new User;
		$user->id = $response->uid;
		$user->email = $response->email;
		$user->accessToken = json_encode($accessToken);
		$name = explode(' ', $response->name, 2);
		$user->firstname = $name[0];
		$user->lastname = $name[1];

		return $user;
	}

	public function endPoint($parameters = array()) {

		if ( !isset($_GET['code'])) {

			Helpers::redirect($this->yammer->getAuthorizationUrl());

		} else {
			// Try to get the access token using auth code
			$accessToken =  $this->yammer->getAccessToken([
				'code' => $_GET['code']
			]);
		}

		return $this->getUser(['accessToken' => $accessToken]);
	}
}

