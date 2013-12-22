<?php

/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Comments;

abstract class Helpers
{

    /**
     *
     * @var Model
     */
    protected static $model;

    protected static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(\ACP3\Core\Registry::get('Db'));
        }
    }

    /**
     * Zählt die Anzahl der Kommentare für einen bestimmten Eintrag eines Modules zusammen
     *
     * @param string $moduleId
     *    Das jeweilige Modul
     * @param integer $resultId
     *    Die ID des jeweiligen Eintrages
     * @return integer
     */
    public static function commentsCount($moduleId, $resultId)
    {
        self::_init();
        return self::$model->countAllByModule($moduleId, $resultId);
    }

    public static function deleteCommentsByModuleAndResult($moduleName, $resultId)
    {
        self::_init();
        return self::$model->delete(array('module_id' => $moduleName, 'entry_id' => $resultId));
    }

}
