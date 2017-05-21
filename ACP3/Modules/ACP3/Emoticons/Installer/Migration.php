<?php

namespace ACP3\Modules\ACP3\Emoticons\Installer;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Emoticons\Installer
 */
class Migration implements \ACP3\Core\Installer\MigrationInterface
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
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            32 => [
                "ALTER TABLE `{pre}emoticons` ENGINE = InnoDB",
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
