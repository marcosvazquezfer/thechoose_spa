<?php
// file: model/Hole.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Hole
*
* Represents a Hole in the web. A Hole is attached
* to a Poll and was created by an specific User
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class Hole {

	/**
	* The id of the poll
	* @var Poll
	*/
	private $poll;

	/**
	* The date of the hole
	* @var date
	*/
	private $date;

	/**
	* The timeStart of the hole
	* @var time
	*/
	private $timeStart;

	/**
	* The timeFinish of the hole
	* @var time
	*/
	private $timeFinish;

	/**
	* The constructor
	*
	* @param Poll $poll The parent poll
	* @param date $date The date of the hole
	* @param time $timeStart The timeStart of the hole
	* @param time $timeFinish The timeFinish post
	*/
	public function __construct(Poll $poll=NULL, $date=NULL, $timeStart=NULL, $timeFinish=NULL) {
        
        $this->poll = $poll;
		$this->date = $date;
		$this->timeStart = $timeStart;
		$this->timeFinish = $timeFinish;
	}

	/**
	* Gets the parent poll of this hole
	*
	* @return Poll The parent poll of this hole
	*/
	public function getPoll() {

		return $this->poll;
	}

	/**
	* Sets the parent Poll
	*
	* @param Poll $poll the parent poll
	* @return void
	*/
	public function setPoll(Poll $poll) {

		$this->poll = $poll;
	}

	/**
	* Gets the date of this hole
	*
	* @return date The date of this hole
	*/
	public function getDate() {

		return $this->date;
	}

	/**
	* Sets the date of the Hole
	*
	* @param date $date the date of this hole
	* @return void
	*/
	public function setDate($date) {

		$this->date = $date;
	}

	/**
	* Gets the timeStart of this hole
	*
	* @return time The timeStart of this hole
	*/
	public function getTimeStart() {

		return $this->timeStart;
	}

	/**
	* Sets the timeStart of this hole
	*
	* @param time $timeStart the timeStart of this hole
	* @return void
	*/
	public function setTimeStart($timeStart){

		$this->timeStart = $timeStart;
	}

	/**
	* Gets the timeFinish of this hole
	*
	* @return time The timeFinish of this hole
	*/
	public function getTimeFinish() {

		return $this->timeFinish;
	}

	/**
	* Sets the timeFinish of this hole
	*
	* @param time $timeFinish the timeFinish of this hole
	* @return void
	*/
	public function setTimeFinish($timeFinish){
        
		$this->timeFinish = $timeFinish;
	}

	/**
	* Checks if the current instance is valid
	* for being inserted in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForCreate() {
		$errors = array();

		if (strlen(trim($this->date)) == NULL ) {

			$errors["date"] = "date is mandatory";
        }
        
		if ($this->timeStart == NULL ) {

			$errors["timeStart"] = "timeStart is mandatory";
        }
        
        if ($this->timeFinish == NULL ) {
            
			$errors["timeFinish"] = "timeFinish is mandatory";
		}

		if ($this->poll == NULL ) {

			$errors["poll"] = "poll is mandatory";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "hole is not valid");
		}
	}
}
