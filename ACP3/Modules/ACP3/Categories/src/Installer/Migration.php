<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Installer;

use ACP3\Core\Modules;
use ACP3\Core\NestedSet\Operation\Repair;

class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * @var \ACP3\Core\NestedSet\Operation\Repair
     */
    private $categoriesNestedSetRepair;

    public function __construct(Repair $categoriesNestedSetRepair)
    {
        $this->categoriesNestedSetRepair = $categoriesNestedSetRepair;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                'ALTER TABLE `{pre}categories` CHANGE `name` `title` VARCHAR(120) {CHARSET} NOT NULL;',
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            33 => [
                'ALTER TABLE `{pre}categories` ENGINE = InnoDB',
            ],
            34 => [
                'DELETE FROM `{pre}categories` WHERE `module_id` NOT IN (SELECT `id` FROM `{pre}modules`);',
                'ALTER TABLE `{pre}categories` ADD INDEX (`module_id`)',
                'ALTER TABLE `{pre}categories` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE',
            ],
            35 => [
                'ALTER TABLE `{pre}categories` MODIFY COLUMN `title` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}categories` MODIFY COLUMN `picture` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}categories` MODIFY COLUMN `description` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}categories` CONVERT TO {charset};',
            ],
            36 => [
                'ALTER TABLE `{pre}categories` ADD COLUMN `root_id` INT(10) UNSIGNED NOT NULL AFTER `id`;',
                'ALTER TABLE `{pre}categories` ADD COLUMN `parent_id` INT(10) UNSIGNED NOT NULL AFTER `root_id`;',
                'ALTER TABLE `{pre}categories` ADD COLUMN `left_id` INT(10) UNSIGNED NOT NULL AFTER `parent_id`;',
                'ALTER TABLE `{pre}categories` ADD COLUMN `right_id` INT(10) UNSIGNED NOT NULL AFTER `left_id`;',
                'ALTER TABLE `{pre}categories` ADD INDEX `left_id` (`left_id`);',
                'UPDATE `{pre}categories` SET `root_id` = `id`, `parent_id` = 0;',
            ],
            37 => [
                function () {
                    $this->categoriesNestedSetRepair->execute();

                    return true;
                },
            ],
            38 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'order', '', 4);",
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}
