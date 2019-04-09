<?php
// file: model/PollMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Poll.php");
require_once(__DIR__."/../model/Hole.php");
require_once(__DIR__."/../model/Participant.php");
require_once(__DIR__."/../model/Selection.php");


/**
* Class PollMapper
*
* Database interface for Poll entities
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class PollMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {

		$this->db = PDOConnection::getInstance();
	}

	/**
	* Retrieves all polls
	*
	* Note: Holes are not added to the Poll instances
	*
	* @throws PDOException if a database error occurs
	* @return mixed Array of Poll instances (without holes)
	*/
	public function findAll() {

		$stmt = $this->db->query("SELECT * FROM polls, users WHERE users.email = polls.email");
		$polls_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$polls = array();

		foreach ($polls_db as $poll) {

			$email = new User($poll["email"]);
			array_push($polls, new Poll($poll["pollId"], $poll["title"], $poll["link"], $email));
		}

		return $polls;
	}

	/**
	* Retrieves all participants
	*/
	public function findAllParticipants() {

		$stmt = $this->db->query("SELECT * FROM participations, users WHERE users.email = participations.email");
		$participants_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$participants = array();

		foreach ($participants_db as $participant) {

			array_push($participants, new Participant($participant["pollId"], $participant["email"]));
		}

		return $participants;
	}

	/**
	* Retrieves all participants of a poll
	*/
	public function findParticipantsByPollId($pollId) {

		$stmt = $this->db->prepare("SELECT * FROM participations WHERE pollId=?");
		$stmt->execute(array($pollId));
		$participants_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$participants = array();

		foreach ($participants_db as $participant) {

			array_push($participants, new Participant(new Poll($participant["pollId"]), new User($participant["email"]), $participant["anonymousId"]));
		}

		return $participants;
	}

	/**
	* Retrieves all participants
	*/
	public function findPollsByEmailParticipant($email) {

		$stmt = $this->db->prepare("SELECT * FROM participations WHERE email=?");
		$stmt->execute(array($email->getEmail()));
		$polls_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$polls = array();

		foreach ($polls_db as $poll) {

			array_push($polls, new Participant(new Poll($poll["pollId"]), new User($poll["email"])));
		}

		return $polls;
	}

	/**
	* Loads a Poll from the database given its id
	*
	* Note: Holes are not added to the Poll
	*
	* @throws PDOException if a database error occurs
	* @return Poll The Poll instances (without holes). NULL
	* if the Poll is not found
	*/
	public function findById($pollId){

		$stmt = $this->db->prepare("SELECT * FROM polls WHERE pollId=?");
		$stmt->execute(array($pollId));
		$poll = $stmt->fetch(PDO::FETCH_ASSOC);

		if($poll != null) {

			return new Poll(
			$poll["pollId"],
			$poll["title"],
			$poll["link"],
			new User($poll["email"]));
		} 
		else {
			return NULL;
		}
	}

	public function findAnonymousById($pollId){

		$stmt = $this->db->prepare("SELECT * FROM polls WHERE pollId=?");
		$stmt->execute(array($pollId));
		$poll = $stmt->fetch(PDO::FETCH_ASSOC);

		if($poll != null) {

			return new Poll(
			$poll["pollId"],
			$poll["title"],
			$poll["link"],
			new User($poll["email"]),
			$poll["anonymous"]);
		} 
		else {
			return NULL;
		}
	}

	/**
	* Loads a Poll from the database given its id and link
	*
	*
	* @throws PDOException if a database error occurs
	* @return Poll The Poll instances (without holes). NULL
	* if the Poll is not found
	*/
	public function findByEmailLink($link, $email){

		$stmt = $this->db->prepare("SELECT * FROM polls WHERE link=? AND email=?");
		$stmt->execute(array($link, $email->getEmail()));
		$poll = $stmt->fetch(PDO::FETCH_ASSOC);

		if($poll != null) {

			return new Poll(
			$poll["pollId"],
			$poll["title"],
			$poll["link"],
			new User($poll["email"]));
		} 
		else {
			return NULL;
		}
	}

	/**
	* Loads a Poll from the database given its id
	*
	* It includes all the holes
	*
	* @throws PDOException if a database error occurs
	* @return Poll The Poll instances (without holes). NULL
	* if the Poll is not found
	*/
	public function findByIdWithHoles($pollId){

		$stmt = $this->db->prepare("SELECT
			P.pollId as 'poll.pollId',
			P.title as 'poll.title',
			P.link as 'poll.link',
			P.email as 'poll.email',
			P.anonymous as 'poll.anonymous',
			H.pollId as 'hole.pollId',
			H.date as 'hole.date',
			H.timeStart as 'hole.timeStart',
			H.timeFinish as 'hole.timeFinish'

			FROM polls P LEFT OUTER JOIN holes H
			ON P.pollId = H.pollId
			WHERE
			P.pollId=? ");

		$stmt->execute(array($pollId));
		$poll_wt_holes= $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (sizeof($poll_wt_holes) > 0) {

			$poll = new Poll($poll_wt_holes[0]["poll.pollId"],
			$poll_wt_holes[0]["poll.title"],
			$poll_wt_holes[0]["poll.link"],
			new User($poll_wt_holes[0]["poll.email"]),
			$poll_wt_holes[0]["poll.anonymous"]);

			$holes_array = array();

			if ($poll_wt_holes[0]["hole.pollId"]!=null) {

				foreach ($poll_wt_holes as $hole){

					$hole = new Hole( new Poll($hole["hole.pollId"]),
					$hole["hole.date"], $hole["hole.timeStart"], $hole["hole.timeFinish"]);

					array_push($holes_array, $hole);
				}
			}

			$poll->setHoles($holes_array);

			return $poll;
		}
		else {
			return NULL;
		}
	}

	/**
	* Loads a Poll from the database given its author
	*
	*
	* @throws PDOException if a database error occurs
	* @return Poll The Poll instances (without holes). NULL
	* if the Poll is not found
	*/
	public function findByEmail($email){

		$stmt = $this->db->prepare("SELECT * FROM polls WHERE email=?");
		$stmt->execute(array($email->getEmail()));
		$polls_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$polls = array();

		foreach ($polls_db as $poll) {

			array_push($polls, new Poll($poll["pollId"], $poll["title"], $poll["link"], new User($poll["email"])));
		}

		return $polls;
	}

	/**
    * Saves a Poll into the database
    *
    * @param Poll $poll The poll to be saved
    * @throws PDOException if a database error occurs
    * @return int The mew poll id
    */
	public function save(Poll $poll) {

		$stmt = $this->db->prepare("INSERT INTO polls(link,email) values (?,?)");
		$stmt->execute(array($poll->getLink(), $poll->getAuthor()->getEmail()));

		return $this->db->lastInsertId();
	}

	/**
    * Updates a Poll in the database
    *
    * @param Poll $poll The poll to be updated
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function update(Poll $poll) {

		$stmt = $this->db->prepare("UPDATE polls SET title=?, anonymous=? WHERE pollId=?");
		$stmt->execute(array($poll->getTitle(), $poll->getAnonymous(), $poll->getPollId()));
	}

	/**
    * Updates Link of a Poll in the database
    *
    * @param Poll $poll The poll to be updated
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function updateLink(Poll $poll) {

		$stmt = $this->db->prepare("UPDATE polls set link=CONCAT(?,?) where pollId=?");
		$stmt->execute(array($poll->getLink(), $poll->getPollId(), $poll->getPollId()));
	}

	/**
    * Saves a participant of a Poll into the database
    *
    * @param $pollId The pollId
    * @param $currentuser The participant to be saved
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function saveParticipant($pollId, $currentuser){

		$stmt = $this->db->prepare("SELECT * FROM participations WHERE pollId=? AND email=?");
		$stmt->execute(array($pollId, $currentuser->getEmail()));
		$participant = $stmt->fetch(PDO::FETCH_ASSOC);

		if($participant == null){

			$stmt = $this->db->prepare("INSERT INTO participations(pollId,email) values (?,?)");
			$stmt->execute(array($pollId, $currentuser->getEmail()));
		}
		else{
			return NULL;
		}
    }
    
    /**
    * Saves the author of a Poll like a participant into the database
    *
    * @param $pollId The pollId
    * @param $currentuser The participant to be saved
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function saveParticipantAdd($pollId, $currentuser){

		$stmt = $this->db->prepare("SELECT * FROM participations WHERE pollId=? AND email=?");
		$stmt->execute(array($pollId->getPollId(), $currentuser->getEmail()));
		$participant = $stmt->fetch(PDO::FETCH_ASSOC);

		if($participant == null){

			$stmt = $this->db->prepare("INSERT INTO participations(pollId,email) values (?,?)");
			$stmt->execute(array($pollId->getPollId(), $currentuser->getEmail()));
		}
		else{
			return NULL;
		}
	}

	/**
	* Loads the participants of a Poll from the database given its id
	*
	*
	* @throws PDOException if a database error occurs
	* @return array The participants of the Poll. NULL
	* if the Poll is not found
	*/
	public function findParticipants($pollId){

		$stmt = $this->db->prepare("SELECT
			P.pollId as 'poll.pollId',
			P.title as 'poll.title',
			P.link as 'poll.link',
			P.email as 'poll.email',
			P.pollId as 'participant.pollId',
			P.email as 'participant.participantId'

			FROM polls P LEFT OUTER JOIN participations P
			ON P.pollId = P.pollId
			WHERE
			P.pollId=? ");

		$stmt->execute(array($pollId));
		$poll_wt_participants= $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (sizeof($poll_wt_participants) > 0) {

			$poll = new Poll($poll_wt_participants[0]["poll.pollId"],
			$poll_wt_participants[0]["poll.title"],
			$poll_wt_participants[0]["poll.link"],
			new User($poll_wt_participants[0]["poll.email"]));

			$participants_array = array();

			if ($poll_wt_participants[0]["participant.pollId"]!=null) {

				foreach ($poll_wt_participants as $participant){

					$participant = new Participant( new Poll($participant["participant.pollId"]),
					$participant["participant.participantId"]);

					array_push($participants_array, $participant);
				}
			}

			$poll->setParticipantes($participants_array);

			return $poll;
		}
		else {
			return NULL;
		}
	}

	/**
	* Loads holes selected of a Poll from the database given its id
	*
	*
	* @throws PDOException if a database error occurs
	* @return Poll The Poll instances. NULL
	* if the Post is not found
	*/
	public function findSelections($pollId){

		$stmt = $this->db->prepare("SELECT
			P.pollId as 'poll.pollId',
			P.title as 'poll.title',
			P.link as 'poll.link',
			P.email as 'poll.email',
			P.anonymous as 'poll.anonymous',
			S.pollId as 'selection.pollId',
			S.date as 'selection.date',
			S.timeStart as 'selection.timeStart',
			S.email as 'selection.email',
			S.anonymousId as 'selection.anonymousId',
			S.selection as 'selection.selection'

			FROM polls P LEFT OUTER JOIN selected S
			ON P.pollId = S.pollId
			WHERE
			P.pollId=? ");

		$stmt->execute(array($pollId));
		$poll_wt_selections= $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (sizeof($poll_wt_selections) > 0) {

			$poll = new Poll($poll_wt_selections[0]["poll.pollId"],
			$poll_wt_selections[0]["poll.title"],
			$poll_wt_selections[0]["poll.link"],
			new User($poll_wt_selections[0]["poll.email"]),
			$poll_wt_selections[0]["poll.anonymous"]);

			$selections_array = array();

			if ($poll_wt_selections[0]["selection.pollId"]!=null) {

				foreach ($poll_wt_selections as $selection){

					$selection = new Selection( new Poll($selection["selection.pollId"]),
					$selection["selection.date"], $selection["selection.timeStart"], $selection["selection.email"], $selection["selection.anonymousId"], $selection["selection.selection"]);

					array_push($selections_array, $selection);
				}
			}

			$poll->setSelections($selections_array);

			return $poll;
		}
		else {
			return NULL;
		}
	}

	/**
	* Loads holes selected of a Poll from the database given its id
	*
	*
	* @throws PDOException if a database error occurs
	* @return Poll The Poll instances. NULL
	* if the Post is not found
	*/
	public function findSelectionByHole($pollId, $date, $timeStart, $email){

		$stmt = $this->db->prepare("SELECT * FROM selected WHERE pollId=? AND date=? AND timeStart=? AND email=?");
		$stmt->execute(array($pollId, $date, $timeStart, $email));
		$selection = $stmt->fetch(PDO::FETCH_ASSOC);

		if($selection != null) {

			return new Selection(
			$selection["pollId"],
			$selection["date"],
			$selection["timeStart"],
			$selection["email"],
			$selection["anonymousId"],
			$selection["selection"]);
		} 
		else {
			return NULL;
		}
	}

	public function findAnonymousSelectionByHole($pollId, $date, $timeStart, $anonymousId){

		$stmt = $this->db->prepare("SELECT * FROM selected WHERE pollId=? AND date=? AND timeStart=? AND anonymousId=?");
		$stmt->execute(array($pollId, $date, $timeStart, $anonymousId));
		$selection = $stmt->fetch(PDO::FETCH_ASSOC);

		if($selection != null) {

			return new Selection(
			$selection["pollId"],
			$selection["date"],
			$selection["timeStart"],
			$selection["email"],
			$selection["anonymousId"],
			$selection["selection"]);
		} 
		else {
			return NULL;
		}
	}

	/**
    * Saves al selections like 0 into the database
    *
    * @param $pollId The id
    * @param $date The date of the hole
    * @param $timeStart The timeStart of the hole
    * @param $email The author of the selection
    * @param $selection The selection
    * @throws PDOException if a database error occurs
    * @return int The mew post id
    */
	public function saveSelectionsIni($pollId, $date, $timeStart, $email, $selection) {

		$stmt = $this->db->prepare("INSERT INTO selected(pollId,date,timeStart,email,selection) values (?,?,?,?,?)");
		$stmt->execute(array($pollId->getPollId(), $date, $timeStart, $email->getEmail(), $selection));

		return $this->db->lastInsertId();
	}

	public function saveAnonymousSelectionsIni($pollId, $date, $timeStart, $anonymousId, $selection) {

		$stmt = $this->db->prepare("INSERT INTO selected(pollId,date,timeStart,anonymousId,selection) values (?,?,?,?,?)");
		$stmt->execute(array($pollId->getPollId(), $date, $timeStart, $anonymousId, $selection));

		return $this->db->lastInsertId();
	}

	/**
    * Updates selections of a Poll in the database
    *
    * @param $pollId The id
    * @param $date The date of the hole
    * @param $timeStart The timeStart of the hole
    * @param $email The author of the selection
    * @param $value The value of the selection
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function updateSelection($pollId, $date, $timeStart, $email, $value) {

		$stmt = $this->db->prepare("UPDATE selected SET selection=? WHERE pollId=? AND date=? AND timeStart=? AND email=?");
		$stmt->execute(array($value, $pollId, $date, $timeStart, $email->getEmail()));

		return $this->db->lastInsertId();
	}

	/**
    * Deletes a Poll into the database
    *
    * @param $pollId The id
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function delete($pollId){

		$stmt = $this->db->prepare("DELETE FROM polls WHERE pollId=?");
		$stmt->execute(array($pollId));
	}

	/**
    * Deletes the participations of an user in a poll
	*
	* @param $currentuser The user
    * @param $pollId The id
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function deleteParticipations($currentUser, $pollId){

		$stmt = $this->db->prepare("DELETE FROM participations WHERE email=? AND pollId=?");
		$stmt->execute(array($currentUser, $pollId));
	}

	public function deleteAnonymousParticipations($anonymousId, $pollId){

		$stmt = $this->db->prepare("DELETE FROM participations WHERE anonymousId=? AND pollId=?");
		$stmt->execute(array($anonymousId, $pollId));
	}

	/**
    * Deletes all selections of a Poll from an user into the database
    *
    * @param $pollId The id
    * @param $currentuser The user
    * @throws PDOException if a database error occurs
    * @return void
    */
	public function deleteSelections($pollId, $currentuser){

		$stmt = $this->db->prepare("DELETE FROM selected WHERE pollId=? AND email=?");
		$stmt->execute(array($pollId, $currentuser));
	}

	public function deleteAnonymousSelections($pollId, $anonymousId){

		$stmt = $this->db->prepare("DELETE FROM selected WHERE pollId=? AND anonymousId=?");
		$stmt->execute(array($pollId, $anonymousId));
	}

	public function saveAnonymousParticipant($pollId, $anonymousId){

		$stmt = $this->db->prepare("INSERT INTO participations(pollId,anonymousId) values (?,?)");
		$stmt->execute(array($pollId, $anonymousId));
	}
	
	public function saveAnonymousSelection($pollId, $date, $timeStart, $anonymousId, $selection){

		$stmt = $this->db->prepare("INSERT INTO selected(pollId,date,timeStart,anonymousId,selection) values (?,?,?,?,?)");
		$stmt->execute(array($pollId, $date, $timeStart, $anonymousId, $selection));
    }
}
