<?php

/**
 * Emoticons
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Emoticons;

use ACP3\Core;

abstract class Helpers
{

    protected static $emoticons = array();

    /**
     *
     * @var Model
     */
    protected static $model;

    protected static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(Core\Registry::get('Db'));
        }
    }

    /**
     * Erzeugt eine Auflistung der Emoticons
     *
     * @param string $formFieldId
     *    Die ID des Eingabefeldes, in welches die Emoticons eingefügt werden sollen
     * @return string
     */
    public static function emoticonsList($formFieldId = '')
    {
        if (empty(self::$emoticons)) {
            self::_init();
            self::$emoticons = self::$model->getEmoticonsCache();
        }

        Core\Registry::get('View')->assign('emoticons_field_id', empty($formFieldId) ? 'message' : $formFieldId);
        Core\Registry::get('View')->assign('emoticons', self::$emoticons);
        return Core\Registry::get('View')->fetchTemplate('emoticons/list.tpl');
    }

    /**
     * Ersetzt bestimmte Zeichen durch Emoticons
     *
     * @param string $string
     *  Zu durchsuchender Text nach Zeichen
     * @return string
     */
    public static function emoticonsReplace($string)
    {
        if (empty(self::$emoticons)) {
            self::_init();
            self::$emoticons = self::$model->getEmoticonsCache();
        }

        return strtr($string, self::$emoticons);
    }

}
