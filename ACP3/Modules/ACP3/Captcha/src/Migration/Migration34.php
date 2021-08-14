<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration34 implements MigrationInterface
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
        return 34;
    }

    public function up(): void
    {
        $this->db->executeQuery("INSERT INTO `{$this->db->getPrefix()}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', (select id from `{$this->db->getPrefix()}modules` WHERE name = 'captcha'), 'recaptcha_sitekey', '');");
        $this->db->executeQuery("INSERT INTO `{$this->db->getPrefix()}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', (select id from `{$this->db->getPrefix()}modules` WHERE name = 'captcha'), 'recaptcha_secret', '');");
    }

    public function down(): void
    {
    }
}
