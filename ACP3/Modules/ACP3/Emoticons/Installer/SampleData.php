<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Installer;

use ACP3\Core\Modules\Installer\AbstractSampleData;

/**
 * Class SampleData
 * @package ACP3\Modules\ACP3\Emoticons\Installer
 */
class SampleData extends AbstractSampleData
{

    /**
     * @return array
     */
    public function sampleData()
    {
        return [
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
        ];
    }
}
