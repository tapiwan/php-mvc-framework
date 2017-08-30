<?php

namespace bitbetrieb\MVC\DatabaseHandler;

/**
 * Class QueryResult
 * @package bitbetrieb\MVC\DatabaseHandler
 */
class QueryResult implements IQueryResult {
	private $success;
	private $data = [];
	private $lastInsertId;

	public function setSuccess($success) {
		$this->success = $success;
	}

	public function addData($data) {
		$this->data[] = $data;
	}

	public function setData(Array $data) {
		foreach ($data as $item) {
			$this->addData($item);
		};
	}

	public function setLastInsertId($lastInsertId) {
		$this->lastInsertId = $lastInsertId;
	}

	public function getSuccess() {
		return $this->success;
	}

	public function getData() {
		return $this->data;
	}

	public function getLastInsertId() {
		return $this->lastInsertId;
	}
}

?>
