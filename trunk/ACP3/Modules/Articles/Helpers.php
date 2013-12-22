<?php

/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Articles;

use ACP3\Core;

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
            self::$model = new Model(Core\Registry::get('Db'));
        }
    }

    /**
     * Gibt alle angelegten Artikel zurück
     *
     * @param integer $id
     * @return array
     */
    public static function articlesList($id = '')
    {
        self::_init();

        $articles = self::$model->getAll();
        $c_articles = count($articles);

        if ($c_articles > 0) {
            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['selected'] = Core\Functions::selectEntry('articles', $articles[$i]['id'], $id);
            }
        }
        return $articles;
    }

}
