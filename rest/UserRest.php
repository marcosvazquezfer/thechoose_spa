<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");
require_once(__DIR__."/BaseRest.php");

/**
* Class UserRest
*
* It contains operations for adding and check users credentials.
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class UserRest extends BaseRest {

	private $userMapper;
	private $pollMapper;

	public function __construct() {
		parent::__construct();

		$this->userMapper = new UserMapper();
		$this->pollMapper = new PollMapper();
	}

	public function postUser($data) {

		$user = new User($data->email, $data->completeName, $data->password);

		try {
			$user->checkIsValidForRegister();

			$this->userMapper->save($user);

			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header("Location: ".$_SERVER['REQUEST_URI']."/".$data->email);
		}
		catch(ValidationException $e) {
			http_response_code(400);
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function login($username) {
		$currentLogged = parent::authenticateUser();
		if ($currentLogged->getEmail() != $username) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not authorized to login as anyone but you");
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
			echo("Hello ".$username);
		}
	}

	public function removeUser() {

		$currentUser = parent::authenticateUser();

		$this->userMapper->delete($currentUser);

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}

	public function postAnonymousUser($data) {

		try {

			$anonymousId = $this->userMapper->saveAnonymousUser($data->completeName);

			$this->pollMapper->saveAnonymousParticipant($data->pollId, $anonymousId);

			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header("Location: ".$_SERVER['REQUEST_URI']."/".$data->completeName);
		}
		catch(ValidationException $e) {
			http_response_code(400);
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}
}

// URI-MAPPING for this Rest endpoint
$userRest = new UserRest();
URIDispatcher::getInstance()
->map("GET",	"/user/$1", array($userRest,"login"))
->map("POST", "/user", array($userRest,"postUser"))
->map("POST", "/user/anonymous", array($userRest,"postAnonymousUser"))
->map("DELETE", "/user", array($userRest,"removeUser"));
