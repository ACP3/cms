<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Installer;

use ACP3\Core\Modules;
use ACP3\Core\Modules\SchemaHelper;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class Migration extends Modules\Installer\AbstractMigration
{
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    private $filesRepository;

    public function __construct(
        SchemaHelper $schemaHelper,
        FilesRepository $filesRepository
    ) {
        parent::__construct($schemaHelper);

        $this->filesRepository = $filesRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                'ALTER TABLE `{pre}files` CHANGE `link_title` `title` VARCHAR(120) {CHARSET} NOT NULL;',
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"extensions/search\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"extensions/feeds\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"functions\";",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "files/", "files/index/") WHERE `uri` LIKE "files/%";',
            ],
            34 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "files/list/", "files/index/index/") WHERE `uri` LIKE "files/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "files/files/", "files/index/files/") WHERE `uri` LIKE "files/files/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "files/details/", "files/index/details/") WHERE `uri` LIKE "files/details/%";' : '',
            ],
            35 => [
                'ALTER TABLE `{pre}files` CHANGE `title` `title` VARCHAR(255) NOT NULL;',
            ],
            36 => [
                'ALTER TABLE `{pre}files` ENGINE = InnoDB',
            ],
            37 => [
                'ALTER TABLE `{pre}files` ADD FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE CASCADE',
            ],
            38 => [
                'ALTER TABLE `{pre}files` CHANGE `user_id` `user_id` INT(10) UNSIGNED',
                'ALTER TABLE `{pre}files` ADD INDEX (`user_id`)',
                'UPDATE `{pre}files` SET `user_id` = NULL WHERE `user_id` = 0',
                'ALTER TABLE `{pre}files` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL',
            ],
            39 => [
                'ALTER TABLE `{pre}files` DROP INDEX `index`;',
                'ALTER TABLE `{pre}files` ADD FULLTEXT `fulltext_index` (`title`, `file`, `text`)',
            ],
            40 => [
                "UPDATE `{pre}acl_resources` SET `area` = 'widget' WHERE `module_id` = '{moduleId}' AND `area` = 'sidebar';",
            ],
            41 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'frontend', 'index', 'download', '', 1);",
            ],
            42 => [
                'ALTER TABLE `{pre}files` ADD COLUMN `updated_at` DATETIME NOT NULL AFTER `end`;',
                'UPDATE `{pre}files` SET `updated_at` = `start`;',
            ],
            43 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'duplicate', '', 4);",
            ],
            44 => [
                'ALTER TABLE `{pre}files` ADD COLUMN `active` TINYINT(1) UNSIGNED NOT NULL AFTER `id`;',
                'ALTER TABLE `{pre}files` ADD INDEX (`active`)',
                'UPDATE `{pre}files` SET `active` = 1;',
            ],
            45 => [
                'ALTER TABLE `{pre}files` ADD COLUMN `sort` INT(10) UNSIGNED NOT NULL AFTER `text`;',
                'ALTER TABLE `{pre}files` ADD INDEX (`sort`)',
                function () {
                    $i = 1;
                    foreach ($this->filesRepository->getAll() as $file) {
                        $this->filesRepository->update(['sort' => $i], $file['id']);

                        ++$i;
                    }
                },
            ],
            46 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'order_by', 'date');",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'sort', '', 4);",
            ],
            47 => [
                'ALTER TABLE `{pre}files` CHANGE `category_id` `category_id` INT(10) UNSIGNED;',
                'ALTER TABLE `{pre}files` DROP FOREIGN KEY `{pre}files_ibfk_1`;',
                'ALTER TABLE `{pre}files` ADD FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE SET NULL',
            ],
            48 => [
                'ALTER TABLE `{pre}files` CONVERT TO {charset};',
                'ALTER TABLE `{pre}files` MODIFY COLUMN `file` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}files` MODIFY COLUMN `size` VARCHAR(20) {charset} NOT NULL;',
                'ALTER TABLE `{pre}files` MODIFY COLUMN `title` VARCHAR(255) {charset} NOT NULL;',
                'ALTER TABLE `{pre}files` MODIFY COLUMN `text` TEXT {charset} NOT NULL;',
            ],
            49 => [
                'ALTER TABLE `{pre}files` DROP COLUMN `comments`;',
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'comments';",
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function renameModule()
    {
        return [];
    }
}
