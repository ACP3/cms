<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Installer;

use ACP3\Core\Installer\MigrationInterface;

class Migration implements MigrationInterface
{
    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            32 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'captcha', 'captcha.extension.native_captcha_extension');",
            ],
            33 => [
                "INSERT INTO `{pre}acl_resources` (`module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ('{moduleId}', 'admin', 'index', 'index', '', 3);",
                "INSERT INTO `{pre}acl_resources` (`module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ('{moduleId}', 'admin', 'index', 'settings', '', 7);",
            ],
            34 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'recaptcha_sitekey', '');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'recaptcha_secret', '');",
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function renameModule()
    {
        return [];
    }
}
