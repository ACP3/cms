<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Installer;

use ACP3\Core\Modules\Installer\AbstractSampleData;

class SampleData extends AbstractSampleData
{
    /**
     * @return array
     */
    public function sampleData()
    {
        return [
            "INSERT INTO `{pre}emoticons` VALUES (1, ':D', 'Very Happy', '1.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (2, ':)', 'Smile', '2.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (3, ':(', 'Sad', '3.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (4, ':o', 'Surprised', '4.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (5', ':shocked:', 'Shocked', '5.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (6, ':?', 'Confused', '6.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (7, ':8)', 'Cool', '7.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (8', ':lol:', 'Laughing', '8.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (9, ':x', 'Mad', '9.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (10, ':P', 'Razz', '10.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (11', ':oops:', 'Embarassed', '11.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (12', ':cry:', 'Crying', '12.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (13', ':evil:', 'Evil', '13.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (14', ':twisted:', 'Twisted Evil', '14.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (15', ':roll:', 'Rolling Eyes', '15.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (16', ':wink:', 'Wink', '16.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (17, ':!:', 'Exclamation', '17.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (18, ':?:', 'Question', '18.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (19', ':idea:', 'Idea', '19.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (20', ':arrow:', 'Arrow', '20.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (21, ':|', 'Neutral', '21.gif');",
            "INSERT INTO `{pre}emoticons` VALUES (22', ':mrgreen:', 'Mr. Green', '22.gif');",
        ];
    }
}
