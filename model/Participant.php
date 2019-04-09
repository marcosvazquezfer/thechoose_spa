<?php
// file: model/Participant.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Participant
*
* Reperesents a participant of a poll
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class Participant {

	/**
	* The id of the poll
	* @var Poll
	*/
	private $pollId;

	/**
	* The id of this participant
	* @var User
	*/
	private $participantId;

	/**
	* The id of this anonymousParticipant
	* @var int
	*/
	private $anonymousId;

    /**
	* The constructor
	*
	* @param int $pollId The id of the poll
	* @param string $participant The participant of a poll
	*/
	public function __construct(Poll $pollId=NULL, User $participantId=NULL, $anonymousId=NULL) {

		$this->pollId = $pollId;
		$this->participantId = $participantId;
		$this->anonymousId = $anonymousId;
	}

	/**
	* Gets the id of this poll
	*
	* @return Poll This poll
	*/
	public function getPollId() {

		return $this->pollId;
	}

	/**
	* Gets the participant of a poll
	*
	* @return User The participant of a poll
	*/
	public function getParticipantId() {

		return $this->participantId;
	}

	/**
	* Sets the participant of a poll
	*
	* @param User $participant the participant of a poll
	* @return void
	*/
	public function setParticipantId(User $participantId) {

		$this->participantId = $participantId;
	}

	public function getAnonymousId() {

		return $this->anonymousId;
	}

	public function setAnonymousId($anonymousId) {

		$this->anonymousId = $anonymousId;
	}
}
