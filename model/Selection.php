<?php
// file: model/Selection.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Selection
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class Selection {

	/**
	* El idENcuesta del hueco
	*/
	private $pollId;

	/**
	* La date del hueco
	*/
	private $date;

	/**
	* La hora de inicio del hueco
	*/
	private $timeStart;

	/**
	* La creador del hueco
	*/
	private $email;

	private $anonymousId;

	/**
	* La selection del hueco
	*/
	private $selection;

	public function __construct($pollId=NULL, $date=NULL, $timeStart=NULL, $email=NULL, $anonymousId=NULL, $selection=NULL) {

		$this->pollId = $pollId;
		$this->date = $date;
		$this->timeStart = $timeStart;
		$this->email = $email;
		$this->anonymousId = $anonymousId;
		$this->selection = $selection;
	}

	/**
	* Obtiene el pollId
	*/
	public function getPollId(){

		return $this->pollId;
	}

	/**
	* Almacena el pollId
	*/
	public function setPollId($pollId) {

		$this->pollId = $pollId;
	}

	/**
	* Obtiene la date
	*/
	public function getDate() {

		return $this->date;
	}

	/**
	* ALmacena la date
	*/
	public function setDate($date) {

		$this->date = $date;
	}

	/**
	* Obtiene la hora de inicio
	*/
	public function getTimeStart() {

		return $this->timeStart;
	}

	/**
	* Almacena la hora de inicio
	*/
	public function setTimeStart($timeStart){
		$this->timeStart = $timeStart;
	}

	/**
	* Obtiene el email delc readore
	*/
	public function getEmail() {

		return $this->email;
	}

	/**
	* Almacena el email del creador
	*/
	public function setEmail($email) {

		$this->email = $email;
	}

	public function getAnonymousId() {

		return $this->anonymousId;
	}

	public function setAnonymousId($anonymousId) {

		$this->anonymousId = $anonymousId;
	}

	/**
	* Obtiene la selection
	*/
	public function getSelection() {

		return $this->selection;
	}

	/**
	* Almacena la selection
	*/
	public function setSelection($selection) {

		$this->selection = $selection;
	}
}
