<?php
// file: model/Post.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Post
*
* Represents a Poll in the web. A Poll was created by an
* specific User (author) and contains a list of Holes
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class Poll {

	/**
	* The id of this poll
	* @var int
	*/
	private $pollId;

	/**
	* The title of this poll
	* @var string
	*/
	private $title;

	/**
	* The link of this poll
	* @var string
	*/
	private $link;

	/**
	* The author of this poll
	* @var User
	*/
	private $author;

	/**
	* If the poll allows anonymous participations
	* @var tinyint
	*/
	private $anonymous;

	/**
	* The list of holes of this poll
	* @var mixed
	*/
    private $holes;
    
    /**
	* The list of participants of this poll
	*/
	private $participants;

	/**
	* The list of holes selected of this poll
	*/
	private $selections;

	/**
	* The constructor
	*
	* @param int $pollId The id of the poll
	* @param string $title The title of the poll
	* @param string $link The link of the poll
	* @param User $author The author of the poll
	* @param tinyint $anonymous If the poll allows anonymous participations
    * @param mixed $holes The list of holes
    * @param mixed $participants The list of participants
    * @param mixed $selections The list of holes selected
	*/
	public function __construct($pollId=NULL, $title=NULL, $link=NULL, User $author=NULL, $anonymous=NULL, array $holes=NULL, array $participants=NULL, array $selections=NULL) {
        
        $this->pollId = $pollId;
		$this->title = $title;
		$this->link = $link;
		$this->author = $author;
		$this->anonymous = $anonymous;
		$this->holes = $holes;
        $this->participants = $participants;
        $this->selections = $selections;
	}

	/**
	* Gets the id of this poll
	*
	* @return int The id of this poll
	*/
	public function getPollId() {

		return $this->pollId;
	}

	/**
	* Gets the title of this poll
	*
	* @return string The title of this poll
	*/
	public function getTitle() {

		return $this->title;
	}

	/**
	* Sets the title of this poll
	*
	* @param string $title the title of this poll
	* @return void
	*/
	public function setTitle($title) {

		$this->title = $title;
	}

	/**
	* Gets the link of this poll
	*
	* @return string The link of this poll
	*/
	public function getLink() {

		return $this->link;
	}

	/**
	* Sets the link of this poll
	*
	* @return void
	*/
	public function setLink() {
		$this->link = "http://localhost:8081/frontend/index.html#view-poll?id=";
	}

	/**
	* Gets the author of this poll
	*
	* @return User The author of this poll
	*/
	public function getAuthor() {

		return $this->author;
	}

	/**
	* Sets the author of this poll
	*
	* @param User $author the author of this poll
	* @return void
	*/
	public function setAuthor(User $author) {

		$this->author = $author;
	}

	public function getAnonymous() {

		return $this->anonymous;
	}

	public function setAnonymous($anonymous) {

		$this->anonymous = $anonymous;
	}

	/**
	* Gets the list of holes of this poll
	*
	* @return mixed The list of holes of this poll
	*/
	public function getHoles() {

		return $this->holes;
	}

	/**
	* Sets the holes of the poll
	*
	* @param mixed $holes the holes list of this poll
	* @return void
	*/
	public function setHoles(array $holes) {

		$this->holes = $holes;
    }
    
   /**
	* Gets the list of participants of this poll
	*
	* @return mixed The list of participants of this poll
	*/
	public function getParticipants() {

		return $this->participants;
	}

	/**
	* Sets the participants of the poll
	*
	* @param mixed $participants the participants list of this poll
	* @return void
	*/
	public function setParticipants(array $participants) {

		$this->participants = $participants;
	}

	/**
	* Gets the list of holes selected of this poll
	*
	* @return mixed The list of holes selected of this poll
	*/
	public function getSelections() {

		return $this->selections;
	}

	/**
	* Sets the holes selected of the poll
	*
	* @param mixed $selections the holes selected list of this poll
	* @return void
	*/
	public function setSelections(array $selections) {

		$this->selections = $selections;
	}

	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForCreate() {

        $errors = array();
        
		if ($this->author == NULL ) {
			$errors["author"] = "author is mandatory";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "post is not valid");
		}
	}

	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForUpdate() {
		$errors = array();

		if (!isset($this->pollId)) {
			$errors["pollId"] = "pollId is mandatory";
		}

		try{
			$this->checkIsValidForCreate();
        }
        catch(ValidationException $ex) {

			foreach ($ex->getErrors() as $key=>$error) {

				$errors[$key] = $error;
			}
        }
        
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "post is not valid");
		}
	}
}
