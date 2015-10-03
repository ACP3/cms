<?php
namespace ACP3\Modules\ACP3\System\Model;

use ACP3\Core\Model;

/**
 * Class SchemaRepository
 * @package ACP3\Modules\ACP3\System\Model
 */
class SchemaRepository extends Model
{
    /**
     * @return array
     */
    public function getSchemaTables()
    {
        return $this->db->fetchAll('SELECT `TABLE_NAME` FROM information_schema.TABLES WHERE `TABLE_TYPE` = ? AND `TABLE_SCHEMA` = ?', ['BASE TABLE', $this->db->getDatabase()]);
    }
}