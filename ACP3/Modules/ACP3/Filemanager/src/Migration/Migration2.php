<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration2 extends AbstractMigration
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
        $this->db->executeQuery("INSERT INTO `{$this->db->getPrefix()}acl_resources` (`module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ((select id from `{$this->db->getPrefix()}modules` WHERE name = 'filemanager'), 'admin', 'index', 'richfilemanager', '', 3);");
    }

    public function down(): void
    {
    }
}
