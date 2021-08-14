<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration32 implements MigrationInterface
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getSchemaVersion(): int
    {
        return 32;
    }

    public function up(): void
    {
        $this->db->executeQuery("UPDATE `{$this->db->getPrefix()}acl_resources` SET `privilege_id` = 3 WHERE `module_id` = (select id from `{$this->db->getPrefix()}modules` WHERE name = 'feeds') AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'index';");
        $this->db->executeQuery("INSERT INTO `{$this->db->getPrefix()}acl_resources` (`module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ((select id from `{$this->db->getPrefix()}modules` WHERE name = 'feeds'), 'admin', 'index', 'settings', '', 7);");
    }

    public function down(): void
    {
    }
}
