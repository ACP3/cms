<?php

namespace ACP3\Modules\ACP3\Polls\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Polls\Installer
 */
class Migration extends Modules\Installer\AbstractMigration
{
    /**
     * @inheritdoc
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "ALTER TABLE `{pre}polls` CHANGE `question` `title` VARCHAR(120) {CHARSET} NOT NULL",
            ],
            32 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "polls/", "polls/index/") WHERE `uri` LIKE "polls/%";',
            ],
            33 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "polls/list/", "polls/index/index/") WHERE `uri` LIKE "polls/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "polls/vote/", "polls/index/vote/") WHERE `uri` LIKE "polls/vote/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "polls/result/", "polls/index/result/") WHERE `uri` LIKE "polls/result/%";' : '',
            ],
            34 => [
                "ALTER TABLE `{pre}polls` ENGINE = InnoDB",
                "ALTER TABLE `{pre}poll_answers` ENGINE = InnoDB",
                "ALTER TABLE `{pre}poll_votes` ENGINE = InnoDB",
            ]
        ];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}
