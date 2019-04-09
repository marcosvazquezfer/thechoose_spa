<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");

/**
* Class UserMapper
*
* Database interface for User entities
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class UserMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {

		$this->db = PDOConnection::getInstance();
	}

	/**
	* Loads the name of an user given its email
	*/
	public function findNameByEmail($email){

		$stmt = $this->db->prepare("SELECT * FROM users WHERE email=?");
		$stmt->execute(array($email));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if($user != null) {

			return new User(
			$user["email"],
			$user["completeName"]);
		} 
		else {
			return NULL;
		}
	}

	public function findNameByAnonymousId($pollId, $anonymousId){

		$stmt = $this->db->prepare("SELECT * FROM anonymous_users, participations WHERE anonymous_users.anonymousId = participations.anonymousId AND anonymous_users.anonymousId=? AND participations.pollId=?");
		$stmt->execute(array($anonymousId, $pollId));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if($user != null) {

			return $user["completeName"];
		} 
		else {
			return NULL;
		}
	}

	public function findAnonymousId($pollId, $completeName){

		$stmt = $this->db->prepare("SELECT * FROM anonymous_users, participations WHERE anonymous_users.anonymousId = participations.anonymousId AND anonymous_users.completeName=? AND participations.pollId=?");
		$stmt->execute(array($completeName, $pollId));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if($user != null) {

			return $user["anonymousId"];
		} 
		else {
			return NULL;
		}
	}

	/**
	* Saves a User into the database
	*
	* @param User $user The user to be saved
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function save($user) {

		$stmt = $this->db->prepare("INSERT INTO users values (?,?,?)");
		$stmt->execute(array($user->getEmail(), $user->getCompleteName(), $user->getPasswd()));
	}

	/**
	* Checks if a given email is already in the database
	*
	* @param string $email the email to check
	* @return boolean true if the email exists, false otherwise
	*/
	public function usernameExists($email) {

		$stmt = $this->db->prepare("SELECT count(email) FROM users where email=?");
		$stmt->execute(array($username));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	/**
    * Deletes an User into the database
    *
    * @param $currentUser The currentUser
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function delete($currentUser){

		$stmt = $this->db->prepare("DELETE FROM users WHERE email=?");
		$stmt->execute(array($currentUser->getEmail()));
	}

	/**
	* Saves an Anonymous User into the database
	*
	* @param string $completeName The name of the anonymous user to be saved
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function saveAnonymousUser($completeName) {

		$stmt = $this->db->prepare("INSERT INTO anonymous_users(completeName) values (?)");
		$stmt->execute(array($completeName));

		return $this->db->lastInsertId();
	}

	/**
	* Checks if a given pair of email/password exists in the database
	*
	* @param string $email the email
	* @param string $passwd the password
	* @return boolean true the email/passwrod exists, false otherwise.
	*/
	public function isValidUser($email, $passwd) {
		$stmt = $this->db->prepare("SELECT count(email) FROM users where email=? and passwd=?");
		$stmt->execute(array($email, $passwd));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}
}
