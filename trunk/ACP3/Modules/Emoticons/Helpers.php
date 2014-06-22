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

    /**
     * @var array
     */
    protected static $emoticons = array();

    /**
     * @var Core\View
     */
    protected static $view;

    protected static function _init()
    {
        if (!self::$view) {
            self::$view = Core\Registry::get('View');

            $model = new Model(Core\Registry::get('Db'));
            $cache = new Cache($model);

            // Initialize emoticons
            self::$emoticons = $cache->getCache();
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
        self::_init();

        self::$view->assign('emoticons_field_id', empty($formFieldId) ? 'message' : $formFieldId);
        self::$view->assign('emoticons', self::$emoticons);
        return self::$view->fetchTemplate('emoticons/emoticons.tpl');
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
        self::_init();

        return strtr($string, self::$emoticons);
    }

}
