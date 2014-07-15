<?php

namespace ACP3\Modules\Polls;

use ACP3\Core\Modules;
use ACP3\Modules\System;
use ACP3\Modules\Permissions;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'polls';
    const SCHEMA_VERSION = 33;

    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        System\Model $systemModel,
        Permissions\Model $permissionsModel,
        Modules $modules
    )
    {
        parent::__construct($db, $systemModel, $permissionsModel);

        $this->modules = $modules;
    }

    public function createTables()
    {
        return array(
            "CREATE TABLE `{pre}polls` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `multiple` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}poll_answers` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `text` VARCHAR(120) NOT NULL,
                `poll_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), INDEX `foreign_poll_id` (`poll_id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}poll_votes` (
                `poll_id` INT(10) UNSIGNED NOT NULL,
                `answer_id` INT(10) UNSIGNED NOT NULL,
                `user_id` INT(10) UNSIGNED NOT NULL,
                `ip` VARCHAR(40) NOT NULL,
                `time` DATETIME NOT NULL,
                INDEX (`poll_id`, `answer_id`, `user_id`)
            ) {engine} {charset};"
        );
    }

    public function removeTables()
    {
        return array(
            "DROP TABLE `{pre}poll_votes`;",
            "DROP TABLE `{pre}poll_answers`;",
            "DROP TABLE `{pre}polls`;"
        );
    }

    public function settings()
    {
        return array();
    }

    public function removeSettings()
    {
        return true;
    }

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "ALTER TABLE `{pre}polls` CHANGE `question` `title` VARCHAR(120) {charset} NOT NULL",
            ),
            32 => array(
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "polls/", "polls/index/") WHERE uri LIKE "polls/%";',
            ),
            33 => array(
                $this->modules->isInstalled('menus') || $this->modules->isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "polls/list/", "polls/index/index/") WHERE uri LIKE "polls/list/%";' : '',
                $this->modules->isInstalled('menus') || $this->modules->isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "polls/vote/", "polls/index/vote/") WHERE uri LIKE "polls/vote/%";' : '',
                $this->modules->isInstalled('menus') || $this->modules->isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "polls/result/", "polls/index/result/") WHERE uri LIKE "polls/result/%";' : '',
            )
        );
    }

}
