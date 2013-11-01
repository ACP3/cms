<?php

namespace ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model {

	protected $db;
	protected $prefix = DB_PRE;

	const TABLE_NAME = '';

	public function __construct(\Doctrine\DBAL\Connection $db) {
		$this->db = $db;
	}

	protected function buildLimitStmt($limitStart = '', $resultsPerPage = '') {
		if (Validate::isNumber($limitStart) === true && Validate::isNumber($resultsPerPage) === true) {
			return ' LIMIT ' . $limitStart . ',' . $resultsPerPage;
		} elseif (Validate::isNumber($limitStart) === true) {
			return ' LIMIT ' . $limitStart;
		} else {
			return '';
		}
	}

	public function insert($params) {
		$this->db->beginTransaction();
		try {
			$this->db->insert($this->prefix . static::TABLE_NAME, $params);
			$lastId = $this->db->lastInsertId();
			$this->db->commit();
			return $lastId;
		} catch (\Exception $e) {
			$this->db->rollback();
		}
	}

	public function delete($id) {
		$this->db->beginTransaction();
		try {
			$bool = $this->db->delete($this->prefix . static::TABLE_NAME, array('id' => (int) $id));
			$this->db->commit();
			return $bool;
		} catch (\Exception $e) {
			$this->db->rollback();
		}
	}

	public function update($params, $id) {
		$this->db->beginTransaction();
		try {
			$bool = $this->db->update($this->prefix . static::TABLE_NAME, $params, array('id' => $id));
			$this->db->commit();
			return $bool;
		} catch (\Exception $e) {
			$this->db->rollback();
		}
	}

}
