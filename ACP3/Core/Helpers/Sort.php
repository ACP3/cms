<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Database\Connection;
use Doctrine\DBAL\DBALException;

class Sort
{
    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;

    /**
     * @param \ACP3\Core\Database\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Moves a database result one step upwards.
     *
     * @param string $table
     * @param string $idField
     * @param string $sortField
     * @param string $id
     * @param string $where
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up($table, $idField, $sortField, $id, $where = '')
    {
        return $this->moveOneStep('up', $table, $idField, $sortField, $id, $where);
    }

    /**
     * Moves a database result one step downwards.
     *
     * @param string $table
     * @param string $idField
     * @param string $sortField
     * @param string $id
     * @param string $where
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function down($table, $idField, $sortField, $id, $where = '')
    {
        return $this->moveOneStep('down', $table, $idField, $sortField, $id, $where);
    }

    /**
     * Moves a database result one step upwards/downwards.
     *
     * @param string $action
     * @param string $table
     * @param string $idField
     * @param string $sortField
     * @param string $id
     * @param string $where
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function moveOneStep($action, $table, $idField, $sortField, $id, $where = '')
    {
        try {
            $this->db->getConnection()->beginTransaction();

            $id = (int) $id;
            $table = $this->db->getPrefix() . $table;

            // Zusätzliche WHERE-Bedingung
            $where = !empty($where) ? 'a.' . $where . ' = b.' . $where . ' AND ' : '';

            // Aktuelles Element und das vorherige Element selektieren
            $query = 'SELECT a.%2$s AS other_id, a.%3$s AS other_sort, b.%3$s AS elem_sort FROM %1$s AS a, %1$s AS b WHERE %5$sb.%2$s = %4$s AND a.%3$s %6$s b.%3$s ORDER BY a.%3$s %7$s LIMIT 1';

            if ($action === 'up') {
                $result = $this->db->getConnection()->fetchAssoc(\sprintf($query, $table, $idField, $sortField, $id, $where, '<', 'DESC'));
            } else {
                $result = $this->db->getConnection()->fetchAssoc(\sprintf($query, $table, $idField, $sortField, $id, $where, '>', 'ASC'));
            }

            if (!empty($result)) {
                // Sortierreihenfolge des aktuellen Elementes zunächst auf 0 setzen
                // um Probleme mit möglichen Duplicate-Keys zu umgehen
                $this->db->getConnection()->update($table, [$sortField => 0], [$idField => $id]);
                $this->db->getConnection()->update($table, [$sortField => $result['elem_sort']], [$idField => $result['other_id']]);
                // Element nun den richtigen Wert zuweisen
                $this->db->getConnection()->update($table, [$sortField => $result['other_sort']], [$idField => $id]);

                $this->db->getConnection()->commit();

                return true;
            }
        } catch (DBALException $e) {
            $this->db->getConnection()->rollBack();

            throw $e;
        }

        return false;
    }
}
