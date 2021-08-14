<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration34 extends AbstractMigration
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
        $this->db->executeQuery("UPDATE `{$this->db->getPrefix()}acl_resources` SET `area` = 'widget' WHERE `module_id` = (select id from `{$this->db->getPrefix()}modules` WHERE name = 'search') AND `area` = 'sidebar';");
    }

    public function down(): void
    {
    }
}
