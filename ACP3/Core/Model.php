<?php

namespace ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model
{

    protected $db;
    protected $lang;
    protected $prefix = DB_PRE;

    const TABLE_NAME = '';

    public function __construct(\Doctrine\DBAL\Connection $db, Lang $lang)
    {
        $this->db = $db;
        $this->lang;
    }

    protected function _buildLimitStmt($limitStart = '', $resultsPerPage = '')
    {
        if (Validate::isNumber($limitStart) === true && Validate::isNumber($resultsPerPage) === true) {
            return ' LIMIT ' . $limitStart . ',' . $resultsPerPage;
        } elseif (Validate::isNumber($limitStart) === true) {
            return ' LIMIT ' . $limitStart;
        } else {
            return '';
        }
    }

    public function insert($params, $tableName = '')
    {
        $this->db->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $this->db->insert($this->prefix . $tableName, $params);
            $lastId = $this->db->lastInsertId();
            $this->db->commit();
            return $lastId;
        } catch (\Exception $e) {
            $this->db->rollback();
        }
    }

    public function delete($id, $field = '', $tableName = '')
    {
        $this->db->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $field = empty($field) ? 'id' : $field;
            $bool = $this->db->delete($this->prefix . $tableName, is_array($id) ? $id : array($field => (int)$id));
            $this->db->commit();
            return $bool;
        } catch (\Exception $e) {
            $this->db->rollback();
        }
    }

    public function update($params, $id, $tableName = '')
    {
        $this->db->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $where = is_array($id) === true ? $id : array('id' => $id);
            $bool = $this->db->update($this->prefix . $tableName, $params, $where);
            $this->db->commit();
            return $bool;
        } catch (\Exception $e) {
            $this->db->rollback();
        }
    }

    public function validateFormKey()
    {
        if (Validate::formToken() === false) {
            throw new Exceptions\InvalidFormToken($this->lang->t('system', 'form_already_submitted'));
        }
    }

}
