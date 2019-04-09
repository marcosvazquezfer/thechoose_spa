<?php
// file: model/HoleMapper.php

require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/Hole.php");

/**
* Class HoleMapper
*
* Database interface for Holes entities
*
* @author Marcos Vázquez Fernández
* @author Lara Souto Alonso
*/
class HoleMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {

		$this->db = PDOConnection::getInstance();
	}

	/**
	* Loads the Holes from the database given its pollId
	*/
	public function findHoles($pollId){

		$stmt = $this->db->prepare("SELECT * FROM holes WHERE pollId=?");
		$stmt->execute(array($pollId));
		$holes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$holes = array();

		if (sizeof($holes_db) > 0) {

			foreach ($holes_db as $hole) {

				array_push($holes, new Hole(new Poll($hole["pollId"]), $hole["date"], $hole["timeStart"], $hole["timeFinish"]));
			}

			return $holes;
		}
		else{
			return NULL;
		}
	}

	/**
	* Loads a Hole from the database given its pollId, date, timeStart, timeFinish
	*/
	public function findHole($pollId, $date, $timeStart, $timeFinish){

		$stmt = $this->db->prepare("SELECT * FROM holes WHERE pollId=? AND date=? AND timeStart=? AND timeFinish=?");
		$stmt->execute(array($pollId, $date, $timeStart, $timeFinish));
		$hole = $stmt->fetch(PDO::FETCH_ASSOC);

		$selection = $stmt->fetch(PDO::FETCH_ASSOC);

		if($hole != null) {

			return new Hole(
			new Poll($hole["pollId"]),
			$hole["date"],
			$hole["timeStart"],
			$hole["timeFinish"]);
		} 
		else {
			return NULL;
		}
	}

	/**
	* Loads a Hole from the database given its pollId, date, timeStart
	*/
	public function findHole2($pollId, $date, $timeStart){

		$stmt = $this->db->prepare("SELECT * FROM holes WHERE pollId=? AND date=? AND timeStart=?");
		$stmt->execute(array($pollId, $date, $timeStart));
		$hole = $stmt->fetch(PDO::FETCH_ASSOC);

		if($hole != null) {

			return new Hole(
			new Poll($hole["pollId"]),
			$hole["date"],
			$hole["timeStart"],
			$hole["timeFinish"]);
		} 
		else {
			return NULL;
		}
	}

	/**
	* Loads the Holes from the database given its pollId and date
	*/
	public function findStarts($pollId, $date){

		$stmt = $this->db->prepare("SELECT * FROM holes WHERE pollId=? AND date=?");
		$stmt->execute(array($pollId, $date));
		$holes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$holes = array();

		if (sizeof($holes_db) > 0) {

			foreach ($holes_db as $hole) {

				array_push($holes, new Hole(new Poll($hole["pollId"]), $hole["date"], $hole["timeStart"], $hole["timeFinish"]));
			}

			return $holes;
		}
		else{
			return NULL;
		}
	}

	/**
    * Saves a hole into the database
    *
    * @param Hole $hole The hole to be saved
    * @throws PDOException if a database error occurs
    * @return int The mew hole id
    */
	public function save(Hole $hole) {

		$stmt = $this->db->prepare("INSERT INTO holes(pollId, date, timeStart, timeFinish) values (?,?,?,?)");
		$stmt->execute(array($hole->getPoll()->getPollId(), $hole->getDate(), $hole->getTimeStart(), $hole->getTimeFinish()));
		
		return $this->db->lastInsertId();
	}

	/**
    * Delete a hole into the database
    *
    * @param $pollId The pollId
    * @param $date The date
    * @param $timeStart The timeStart
    * @param $timeFinish The timeFinish
    * @throws PDOException if a database error occurs
    */
	public function delete($pollId, $date, $timeStart, $timeFinish) {

		$stmt = $this->db->prepare("DELETE FROM holes WHERE pollId=? AND date=? AND timeStart=? AND timeFinish=?");
		$stmt->execute(array($pollId, $date, $timeStart, $timeFinish));
	}

	/**
    * Update a hole into the database
    *
    * @param Hole $hole The hole to be updated
    * @throws PDOException if a database error occurs
    * @return int The mew hole id
    */
	public function edit($pollId, $date, $timeStart, $timeFinish, $oldDate, $oldTimeStart) {

		$stmt = $this->db->prepare("UPDATE holes SET date=?, timeStart=?, timeFinish=? WHERE pollId=? AND date=? AND timeStart=?");
		$stmt->execute(array($date, $timeStart, $timeFinish, $pollId, $oldDate, $oldTimeStart));
		
		return $this->db->lastInsertId();
	}
}
