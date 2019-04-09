<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Poll.php");
require_once(__DIR__."/../model/PollMapper.php");

require_once(__DIR__."/../model/Hole.php");
require_once(__DIR__."/../model/HoleMapper.php");

require_once(__DIR__."/BaseRest.php");

/**
* Class PollRest
*
* It contains operations for creating, retrieving, updating, deleting and
* listing polls, as well as to create holes to polls.
*
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class PollRest extends BaseRest {

	private $pollMapper;
	private $holeMapper;
	private $userMapper;

	public function __construct() {

		parent::__construct();

		$this->pollMapper = new PollMapper();
		$this->holeMapper = new HoleMapper();
		$this->userMapper = new UserMapper();
	}

	public function getPolls() {

		$polls = $this->pollMapper->findAll();

		// json_encode Poll objects.
		// since Poll objects have private fields, the PHP json_encode will not
		// encode them, so we will create an intermediate array using getters and
		// encode it finally
        $polls_array = array();
        
		foreach($polls as $poll) {

			array_push($polls_array, array(
				"pollId" => $poll->getPollId(),
				"title" => $poll->getTitle(),
				"link" => $poll->getLink(),
				"author_email" => $poll->getAuthor()->getEmail()
			));
		}

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($polls_array));
    }
    
    public function getMyPolls() {

		$currentUser = parent::authenticateUser();

		$creations = $this->pollMapper->findByEmail($currentUser);
		$pollsId = $this->pollMapper->findPollsByEmailParticipant($currentUser);

		$myPolls_array = array();

		foreach($creations as $creation) {

			array_push($myPolls_array, array(
				"pollId" => $creation->getPollId(),
				"title" => $creation->getTitle(),
				"link" => $creation->getLink(),
				"author_email" => $creation->getAuthor()->getEmail()
			));
		}

		foreach($pollsId as $pollId){

			$participation = $this->pollMapper->findById($pollId->getPollId()->getPollId());

			array_push($myPolls_array, array(
				"pollId" => $participation->getPollId(),
				"title" => $participation->getTitle(),
				"link" => $participation->getLink(),
				"author_email" => $participation->getAuthor()->getEmail()
			));
		}

		// json_encode Poll objects.
		// since Poll objects have private fields, the PHP json_encode will not
		// encode them, so we will create an intermediate array using getters and
		// encode it finally

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($myPolls_array));
	}

	public function createPoll() {

		$currentUser = parent::authenticateUser();
		$poll = new Poll();

		$poll->setLink();

        $poll->setAuthor($currentUser);
        
        $pollLink = $poll->getLink();

		try {
			// validate Poll object
			$poll->checkIsValidForCreate(); // if it fails, ValidationException

			// save the Poll object into the database
            $this->pollMapper->save($poll);
            
            // loads a poll given its author and link
            $pollId = $this->pollMapper->findByEmailLink($pollLink, $currentUser);
            
            // put the correct link of the Poll object into the database
            $this->pollMapper->updateLink($pollId);
            
            $this->pollMapper->saveParticipantAdd($pollId, $currentUser);

			// response OK. Also send poll in content
			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header('Location: '.$_SERVER['REQUEST_URI']."/".$pollId->getPollId());
			header('Content-Type: application/json');
			echo(json_encode(array(
				"pollId"=>$pollId->getPollId(),
				"link"=>$poll->getLink()
			)));

        } 
        catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function readPoll($pollId) {

		$currentUser = parent::authenticateUser();

		// find the Poll object in the database
		$poll = $this->pollMapper->findByIdWithHoles($pollId);

		//
		$this->pollMapper->saveParticipant($pollId, $currentUser);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		$poll_array = array(
			"pollId" => $poll->getPollId(),
			"title" => $poll->getTitle(),
			"link" => $poll->getLink(),
			"author_email" => $poll->getAuthor()->getEmail(),
			"anonymous" => $poll->getAnonymous()
		);

		//add holes
		$poll_array["holes"] = array();

		foreach ($poll->getHoles() as $hole) {

			$participants = $this->pollMapper->findParticipantsByPollId($poll->getPollId());

			foreach($participants as $participant){
				/*** ToDo: IMPLEMENTAR findSelectionByHole ***/
				if($participant->getParticipantId()->getEmail() != NULL){
					$selection = $this->pollMapper->findSelectionByHole($hole->getPoll()->getPollId(), $hole->getDate(), $hole->getTimeStart(), $participant->getParticipantId()->getEmail());
					
					if($selection == NULL){
						
						$selection = 0;
						$this->pollMapper->saveSelectionsIni($hole->getPoll(), $hole->getDate(), $hole->getTimeStart(), $participant->getParticipantId(), $selection);
					}
				}
				else{
					$selection = $this->pollMapper->findAnonymousSelectionByHole($hole->getPoll()->getPollId(), $hole->getDate(), $hole->getTimeStart(), $participant->getAnonymousId());
					
					if($selection == NULL){
						
						$selection = 0;
						$this->pollMapper->saveAnonymousSelectionsIni($hole->getPoll(), $hole->getDate(), $hole->getTimeStart(), $participant->getAnonymousId(), $selection);
					}
				}
			}

			array_push($poll_array["holes"], array(
				"pollId" => $hole->getPoll()->getPollId(),
				"date" => $hole->getDate(),
				"timeStart" => $hole->getTimeStart(),
				"timeFinish" => $hole->getTimeFinish()
			));
		}

		//add participants
		$poll_array["participants"] = array();

		$participants = $this->pollMapper->findParticipantsByPollId($poll->getPollId());
		
		foreach ($participants as $participant) {

			if($participant->getParticipantId()->getEmail() == null){

				$nameAnonymous = $this->userMapper->findNameByAnonymousId($pollId, $participant->getAnonymousId());

				array_push($poll_array["participants"], array(
					"participantId" => $participant->getAnonymousId(),
					"completeName" => $nameAnonymous
				));
			}
			else{
				$user = $this->userMapper->findNameByEmail($participant->getParticipantId()->getEmail());

				array_push($poll_array["participants"], array(
					"participantId" => $user->getEmail(),
					"completeName" => $user->getCompleteName()
				));
			}

			
		}

		//add selections
		$poll_array["selections"] = array();

		$selections = $this->pollMapper->findSelections($poll->getPollId());
        
		foreach ($selections->getSelections() as $selection) {

			if($selection->getEmail() == NULL){

				array_push($poll_array["selections"], array(
					"date" => $selection->getDate(),
					"timeStart" => $selection->getTimeStart(),
					"email" => $selection->getAnonymousId(),
					"selection" => $selection->getSelection()
				));
			}
			else{
				array_push($poll_array["selections"], array(
					"date" => $selection->getDate(),
					"timeStart" => $selection->getTimeStart(),
					"email" => $selection->getEmail(),
					"selection" => $selection->getSelection()
				));
			}
		}

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($poll_array));
	}

	public function updatePoll($pollId, $data) {

		$currentUser = parent::authenticateUser();

        $poll = $this->pollMapper->findById($pollId);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {

			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of this poll");
			return;
        }
        
		$poll->setTitle($data->title);
		$poll->setAnonymous($data->anonymous);

		try {
			// validate Poll object
			$poll->checkIsValidForUpdate(); // if it fails, ValidationException
			$this->pollMapper->update($poll);

			$poll = $this->pollMapper->findByIdWithHoles($pollId);

			$poll_array = array(
				"pollId" => $poll->getPollId(),
				"title" => $poll->getTitle(),
				"link" => $poll->getLink(),
				"author_email" => $poll->getAuthor()->getEmail(),
				"anonymous" => $poll->getAnonymous()
			);
	
			//add holes
			$poll_array["holes"] = array();
	
			foreach ($poll->getHoles() as $hole) {
	
				array_push($poll_array["holes"], array(
					"pollId" => $hole->getPoll()->getPollId(),
					"date" => $hole->getDate(),
					"timeStart" => $hole->getTimeStart(),
					"timeFinish" => $hole->getTimeFinish()
				));
			}

            header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
            header('Content-Type: application/json');
            echo(json_encode(array($poll_array)));
        }
        catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function participatePoll($pollId, $data) {

		$currentUser = parent::authenticateUser();

        $poll = $this->pollMapper->findById($pollId);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}
        
		$poll->setTitle($data->title);

		try {
			// validate Poll object
			$poll->checkIsValidForUpdate(); // if it fails, ValidationException
			$this->pollMapper->update($poll);
            header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
            header('Content-Type: application/json');
            echo(json_encode(array(
                "pollId"=>$poll->getPollId(),
				"title"=>$poll->getTitle()
			)));
        }
        catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function findHoles($pollId) {

		$currentUser = parent::authenticateUser();

		// find the Poll object in the database
		$holes = $this->holeMapper->findHoles($pollId);

		$poll = $this->pollMapper->findById($pollId);
        
		if ($holes == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Holes of Poll with id ".$pollId." not found");
			return;
		}

		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of the poll");
			return;
		}

		$holes_array = array();

		foreach($holes as $hole){
			
			array_push($holes_array, array(
				"pollId" => $hole->getPoll()->getPollId(),
				"date" => $hole->getDate(),
				"timeStart" => $hole->getTimeStart(),
				"timeFinish" => $hole->getTimeFinish()
			));
		}

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($holes_array));
	}

	public function createHole($pollId, $data) {

		$currentUser = parent::authenticateUser();

        $poll = $this->pollMapper->findById($pollId);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of the poll");
			return;
		}

		$hole = new Hole();
		$hole->setPoll($poll);
        $hole->setDate($data->date);
        $hole->setTimeStart($data->timeStart);
        $hole->setTimeFinish($data->timeFinish);

		try {
			$hole->checkIsValidForCreate(); // if it fails, ValidationException

			$this->holeMapper->save($hole);

            header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
            header('Content-Type: application/json');
            echo(json_encode(array(
				"pollId"=>$hole->getPoll()->getPollId(),
                "date"=>$hole->getDate(),
                "timeStart"=>$hole->getTimeStart(),
                "timeFinish"=>$hole->getTimeFinish()
			)));
        }
        catch(ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
    }
    
    public function deleteHole($pollId, $data) {

		$currentUser = parent::authenticateUser();
		
		$poll = $this->pollMapper->findById($pollId);
        
		$hole = $this->holeMapper->findHole($pollId, $data->date, $data->timeStart, $data->timeFinish);

		if ($hole == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Hole with pollId ".$pollId.", date ".$data->date.", timeStart ".$data->timeStart.", timeFinish ".$data->timeFinish." not found");
			return;
		}
		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of the poll");
			return;
		}

		$this->holeMapper->delete($hole->getPoll()->getPollId(), $hole->getDate(), $hole->getTimeStart(), $hole->getTimeFinish());

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}

	public function updateSelection($pollId, $data) {

		$currentUser = parent::authenticateUser();

		$selection = $this->pollMapper->findSelectionByHole($pollId, $data->date, $data->timeStart, $currentUser->getEmail());

		if($selection == NULL){

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Selection with pollId ".$pollId.", date ".$data->date.", timeStart ".$data->timeStart.", user ".$currentUser->getEmail()." not found");
			return;
		}

		try {
			// validate Poll object
			//$poll->checkIsValidForUpdate(); // if it fails, ValidationException
			//$selection = 1;
			$this->pollMapper->updateSelection($pollId, $data->date, $data->timeStart, $currentUser, $data->selection);

            header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
        }
        catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function deletePoll($pollId) {

		$currentUser = parent::authenticateUser();
		
		$poll = $this->pollMapper->findById($pollId);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}
		
		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of the poll with this hole");
			return;
		}

		$this->pollMapper->delete($poll->getPollId());

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}

	public function findHole($pollId, $date, $timeStart) {

		$currentUser = parent::authenticateUser();

		$poll = $this->pollMapper->findById($pollId);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of the poll with this hole");
			return;
		}

		// find the Hole object in the database
		$hole = $this->holeMapper->findHole2($pollId, $date, $timeStart);
        
		if ($hole == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Hole of Poll with id ".$pollId." not found");
			return;
		}

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode(array(
				"pollId"=>$hole->getPoll()->getPollId(), 
				"date"=>$hole->getDate(), 
				"timeStart"=>$hole->getTimeStart(), 
				"timeFinish"=>$hole->getTimeFinish()
		)));
	}

	public function editHole($pollId, $data) {

		$currentUser = parent::authenticateUser();

        $poll = $this->pollMapper->findById($pollId);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of the poll with this hole");
			return;
		}

		// find the Hole object in the database
		$hole = $this->holeMapper->findHole2($pollId, $data->oldDate, $data->oldTimeStart);
        
		if ($hole == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Hole of Poll with id ".$pollId." not found");
			return;
		}

		try {
			// validate Poll object
			//$poll->checkIsValidForUpdate(); // if it fails, ValidationException
			//$selection = 1;
			$this->holeMapper->edit($pollId, $data->date, $data->timeStart, $data->timeFinish, $data->oldDate, $data->oldTimeStart);

            header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
        }
        catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}
	
	public function unsubscribeUser($pollId){

		$currentUser = parent::authenticateUser();
		
		$poll = $this->pollMapper->findById($pollId);

		if ($poll == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		$this->pollMapper->deleteParticipations($currentUser->getEmail(), $pollId);
		$this->pollMapper->deleteSelections($pollId, $currentUser->getEmail());

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}

	public function removeUser($pollId, $participantId){

		$currentUser = parent::authenticateUser();
		
		$poll = $this->pollMapper->findById($pollId);

		if ($poll == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		// Check if the Poll author is the currentUser (in Session)
		if ($poll->getAuthor()->getEmail() != $currentUser->getEmail()) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of the poll");
			return;
		}

		if($participantId.is_integer()){
			$this->pollMapper->deleteAnonymousParticipations($participantId, $pollId);
			$this->pollMapper->deleteAnonymousSelections($pollId, $participantId);
		}
		else{
			$this->pollMapper->deleteParticipations($participantId, $pollId);
			$this->pollMapper->deleteSelections($pollId, $participantId);
		}
		

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}

	public function findAnonymous($pollId){

		$poll = $this->pollMapper->findAnonymousById($pollId);

		if ($poll == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		$poll_array = array(
			"pollId" => $poll->getPollId(),
			"title" => $poll->getTitle(),
			"link" => $poll->getLink(),
			"author_email" => $poll->getAuthor()->getEmail(),
			"anonymous" => $poll->getAnonymous()
		);

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($poll_array));
	}

	public function readAnonymousPoll($pollId) {

		// find the Poll object in the database
		$poll = $this->pollMapper->findByIdWithHoles($pollId);
        
		if ($poll == NULL) {

			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Poll with id ".$pollId." not found");
			return;
		}

		$poll_array = array(
			"pollId" => $poll->getPollId(),
			"title" => $poll->getTitle(),
			"link" => $poll->getLink(),
			"author_email" => $poll->getAuthor()->getEmail(),
			"anonymous" => $poll->getAnonymous()
		);

		//add holes
		$poll_array["holes"] = array();
		/*** AQUI ESTA LO QUE TENGO QUE MIRAR ***/
		foreach ($poll->getHoles() as $hole) {

			array_push($poll_array["holes"], array(
				"pollId" => $hole->getPoll()->getPollId(),
				"date" => $hole->getDate(),
				"timeStart" => $hole->getTimeStart(),
				"timeFinish" => $hole->getTimeFinish()
			));
		}

		//add participants
		$poll_array["participants"] = array();

		$participants = $this->pollMapper->findParticipantsByPollId($poll->getPollId());

		foreach ($participants as $participant) {

			if($participant->getParticipantId()->getEmail() == NULL){

				$name = $this->userMapper->findNameByAnonymousId($pollId, $participant->getAnonymousId());

				array_push($poll_array["participants"], array(
					"participantId" => $participant->getAnonymousId(),
					"completeName" => $name
				));
			}
			else{
				$user = $this->userMapper->findNameByEmail($participant->getParticipantId()->getEmail());

				array_push($poll_array["participants"], array(
					"participantId" => $user->getEmail(),
					"completeName" => $user->getCompleteName()
				));
			}

			
		}

		//add selections
		$poll_array["selections"] = array();

		$selections = $this->pollMapper->findSelections($poll->getPollId());
        
		foreach ($selections->getSelections() as $selection) {

			if($selection->getEmail() == NULL){

				array_push($poll_array["selections"], array(
					"date" => $selection->getDate(),
					"timeStart" => $selection->getTimeStart(),
					"email" => $selection->getAnonymousId(),
					"selection" => $selection->getSelection()
				));
			}
			else{
				array_push($poll_array["selections"], array(
					"date" => $selection->getDate(),
					"timeStart" => $selection->getTimeStart(),
					"email" => $selection->getEmail(),
					"selection" => $selection->getSelection()
				));
			}
		}

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($poll_array));
	}

	public function addAnonymousSelection($pollId, $data) {

		$anonymousId = $this->userMapper->findAnonymousId($pollId, $data->completeName);

		try {
			// validate Poll object
			//$poll->checkIsValidForUpdate(); // if it fails, ValidationException
			//$selection = 1;
			$this->pollMapper->saveAnonymousSelection($pollId, $data->date, $data->timeStart, $anonymousId, $data->selection);

            header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
        }
        catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}
}

// URI-MAPPING for this Rest endpoint
$pollRest = new PollRest();
URIDispatcher::getInstance()
/*->map("GET",	"/poll", array($pollRest,"getPolls"))*/
->map("GET",	"/poll", array($pollRest,"getMyPolls"))
->map("GET",	"/poll/$1", array($pollRest,"readPoll"))
->map("GET",	"/poll/$1/hole", array($pollRest,"findHoles"))
->map("GET",	"/poll/$1/hole/$2/$3", array($pollRest,"findHole"))
->map("GET",	"/poll/$1/anonymousPoll", array($pollRest,"findAnonymous"))
->map("GET",	"/poll/$1/anonymous", array($pollRest,"readAnonymousPoll"))
->map("POST", 	"/poll", array($pollRest,"createPoll"))
->map("POST", 	"/poll/$1/hole", array($pollRest,"createHole"))
->map("POST",	"/poll/$1/selection/anonymous", array($pollRest,"addAnonymousSelection"))
->map("DELETE",	"/poll/$1", array($pollRest,"deletePoll"))
->map("DELETE", "/poll/$1/hole", array($pollRest,"deleteHole"))
->map("DELETE", "/poll/$1/user", array($pollRest,"unsubscribeUser"))
->map("DELETE", "/poll/$1/user/$2", array($pollRest,"removeUser"))
->map("PUT",	"/poll/$1", array($pollRest,"updatePoll"))
->map("PUT",	"/poll/$1/selection", array($pollRest,"updateSelection"))
->map("PUT", 	"/poll/$1/hole", array($pollRest,"editHole"));

