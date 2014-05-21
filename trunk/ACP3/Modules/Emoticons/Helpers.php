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

    /**
     * @var Core\View
     */
    protected static $view;

    protected static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(Core\Registry::get('Db'), Core\Registry::get('Lang'));
            self::$view = Core\Registry::get('View');

            // Initialize emoticons
            self::$emoticons = self::$model->getCache();
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
