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

    private static $emoticons = array();

    /**
     *
     * @var Model
     */
    private static $model;

    private static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(Core\Registry::get('Db'));
        }
    }

    /**
     * Cache die Emoticons
     *
     * @return boolean
     */
    public static function setEmoticonsCache()
    {
        self::_init();
        $emoticons = self::$model->getAll();
        $c_emoticons = count($emoticons);

        $data = array();
        for ($i = 0; $i < $c_emoticons; ++$i) {
            $picInfos = getimagesize(UPLOADS_DIR . 'emoticons/' . $emoticons[$i]['img']);
            $code = $emoticons[$i]['code'];
            $description = $emoticons[$i]['description'];
            $data[$code] = '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $emoticons[$i]['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
        }

        return Core\Cache::create('list', $data, 'emoticons');
    }

    /**
     * Bindet die gecacheten Emoticons ein
     *
     * @return array
     */
    public static function getEmoticonsCache()
    {
        if (Core\Cache::check('list', 'emoticons') === false) {
            self::setEmoticonsCache();
        }

        return Core\Cache::output('list', 'emoticons');
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
            self::$emoticons = self::getEmoticonsCache();
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
            self::$emoticons = self::getEmoticonsCache();
        }

        return strtr($string, self::$emoticons);
    }

}
