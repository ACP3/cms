<?php

/**
 * Articles
 *
 * @author     Tino Goratsch
 * @package    ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Articles;

use ACP3\Core;

class Helpers
{
    const URL_KEY_PATTERN = 'articles/index/details/id_%s/';
    /**
     * @var Model
     */
    protected $articlesModel;

    public function __construct(Model $articlesModel)
    {
        $this->articlesModel = $articlesModel;
    }

    /**
     * Gibt alle angelegten Artikel zurück
     *
     * @param integer $id
     *
     * @return array
     */
    public function articlesList($id = 0)
    {
        $articles = $this->articlesModel->getAll();
        $c_articles = count($articles);

        if ($c_articles > 0) {
            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['selected'] = Core\Functions::selectEntry('articles', $articles[$i]['id'], $id);
            }
        }
        return $articles;
    }

    public function articleExists($id)
    {
        return $this->articlesModel->resultExists($id);
    }

}