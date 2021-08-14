<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration45 extends AbstractMigration
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
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}news` DROP COLUMN `comments`;");
        $this->db->executeQuery("DELETE FROM `{$this->db->getPrefix()}settings` WHERE `module_id` = (select id from `{$this->db->getPrefix()}modules` WHERE name = 'news') AND `name` = 'comments';");
    }

    public function down(): void
    {
    }
}
