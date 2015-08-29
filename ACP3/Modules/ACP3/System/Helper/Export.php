<?php
namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core;

/**
 * Class Export
 * @package ACP3\Modules\ACP3\System\Helper
 */
class Export
{
    /**
     * @var Core\DB
     */
    protected $db;

    /**
     * @param \ACP3\Core\DB $db
     */
    public function __construct(Core\DB $db)
    {
        $this->db = $db;
    }

    /**
     * @param array  $tables
     * @param string $exportType
     * @param bool   $withDropTables
     *
     * @return string
     */
    public function exportDatabase(array $tables, $exportType, $withDropTables)
    {
        $structure = $data = '';
        foreach ($tables as $table) {
            // Struktur ausgeben
            if ($exportType === 'complete' || $exportType === 'structure') {
                $result = $this->db->fetchAssoc("SHOW CREATE TABLE {$table}");
                if (!empty($result)) {
                    $structure .= $withDropTables == 1 ? "DROP TABLE IF EXISTS `{$table}`;\n\n" : '';
                    $structure .= $result['Create Table'] . ';' . "\n\n";
                }
            }

            // Datensätze ausgeben
            if ($exportType === 'complete' || $exportType === 'data') {
                $resultSets = $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . substr($table, strlen($this->db->getPrefix())));
                if (count($resultSets) > 0) {
                    $fields = '';
                    // Felder der jeweiligen Tabelle auslesen
                    foreach (array_keys($resultSets[0]) as $field) {
                        $fields .= '`' . $field . '`, ';
                    }

                    // Datensätze auslesen
                    foreach ($resultSets as $row) {
                        $values = '';
                        foreach ($row as $value) {
                            $values .= '\'' . $value . '\', ';
                        }
                        $data .= "INSERT INTO `{$table}` ({substr($fields, 0, -2)}) VALUES ({substr($values, 0, -2)});\n";
                    }
                }
            }
        }

        return $structure . $data;
    }
}