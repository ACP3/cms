<?php

namespace ACP3\Modules\Emoticons;

use ACP3\Core\Modules;

class Installer extends Modules\Installer
{

    const MODULE_NAME = 'emoticons';
    const SCHEMA_VERSION = 31;

    protected function createTables()
    {
        return array(
            "CREATE TABLE `{pre}emoticons` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `code` VARCHAR(10) NOT NULL,
                `description` VARCHAR(15) NOT NULL,
                `img` VARCHAR(40) NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            // Default Smilies
            "INSERT INTO `{pre}emoticons` VALUES ('', ':D', 'Very Happy', '1.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':)', 'Smile', '2.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':(', 'Sad', '3.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':o', 'Surprised', '4.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':shocked:', 'Shocked', '5.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':?', 'Confused', '6.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':8)', 'Cool', '7.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':lol:', 'Laughing', '8.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':x', 'Mad', '9.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':P', 'Razz', '10.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':oops:', 'Embarassed', '11.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':cry:', 'Crying', '12.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':evil:', 'Evil', '13.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':twisted:', 'Twisted Evil', '14.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':roll:', 'Rolling Eyes', '15.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':wink:', 'Wink', '16.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':!:', 'Exclamation', '17.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':?:', 'Question', '18.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':idea:', 'Idea', '19.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':arrow:', 'Arrow', '20.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':|', 'Neutral', '21.gif');",
            "INSERT INTO `{pre}emoticons` VALUES ('', ':mrgreen:', 'Mr. Green', '22.gif');"
        );
    }

    protected function removeTables()
    {
        return array("DROP TABLE `{pre}emoticons`;");
    }

    protected function settings()
    {
        return array(
            'width' => 32,
            'height' => 32,
            'filesize' => 10240,
        );
    }

    protected function schemaUpdates()
    {
        return array(
            31 => array(
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            )
        );
    }

}
