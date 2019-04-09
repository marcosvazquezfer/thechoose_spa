<?php
// file: model/User.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class User
*
* Represents a User in the web
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class User {

	/**
	* The email of the user
	* @var string
	*/
	private $email;

	/**
	* The complete name of the user
	* @var string
	*/
	private $completeName;

	/**
	* The password of the user
	* @var string
	*/
	private $passwd;

	/**
	* The constructor
	*
	* @param string $email The email of the user
	* @param string $completeName The complete name of the user
	* @param string $passwd The password of the user
	*/
	public function __construct($email=NULL, $completeName=NULL, $passwd=NULL) {

		$this->email = $email;
		$this->completeName = $completeName;
		$this->passwd = $passwd;
	}

	/**
	* Gets the email of this user
	*
	* @return string The email of this user
	*/
	public function getEmail() {

		return $this->email;
	}

	/**
	* Sets the email of this user
	*
	* @param string $email The email of this user
	* @return void
	*/
	public function setEmail($email) {

		$this->email = $email;
	}

	/**
	* Gets the completename of this user
	*
	* @return string The completename of this user
	*/
	public function getCompleteName() {

		return $this->completeName;
	}

	/**
	* Sets the completename of this user
	*
	* @param string $completeName The completename of this user
	* @return void
	*/
	public function setCompleteName($completeName) {

		$this->completeName = $completeName;
	}

	/**
	* Gets the password of this user
	*
	* @return string The password of this user
	*/
	public function getPasswd() {

		return $this->passwd;
	}

	/**
	* Sets the password of this user
	*
	* @param string $passwd The password of this user
	* @return void
	*/
	public function setPassword($passwd) {

		$this->passwd = $passwd;
	}

	/**
	* Checks if the current user instance is valid
	* for being registered in the database
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForRegister() {
		$errors = array();

		if (strlen($this->email) < 5) {
			$errors["email"] = "Email must be at least 5 characters length";

		}

		if (strlen($this->completeName) < 5) {
			$errors["completeName"] = "Complete Name must be at least 5 characters length";

		}

		if (strlen($this->passwd) < 5) {
			$errors["passwd"] = "Password must be at least 5 characters length";
		}

		if (sizeof($errors)>0){
			throw new ValidationException($errors, "user is not valid");
		}
	}
}
