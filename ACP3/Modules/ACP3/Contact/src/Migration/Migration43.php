<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration43 extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $this->db->executeQuery("INSERT INTO `{$this->db->getPrefix()}acl_resources` (`module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ((select id from `{$this->db->getPrefix()}modules` WHERE name = 'contact'), 'admin', 'index', 'delete', '', 6);",
        );
    }

    public function down(): void
    {
    }
}
