<?php
namespace ACP3\Modules\Articles;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Articles
 */
class Helpers
{
    const URL_KEY_PATTERN = 'articles/index/details/id_%s/';
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Model
     */
    protected $articlesModel;

    /**
     * @param Core\Helpers\Forms $formsHelper
     * @param Model $articlesModel
     */
    public function __construct(
        Core\Helpers\Forms $formsHelper,
        Model $articlesModel
    )
    {
        $this->formsHelper = $formsHelper;
        $this->articlesModel = $articlesModel;
    }

    /**
     * Gibt alle angelegten Artikel zurück
     *
     * @param integer $id
     * @return array
     */
    public function articlesList($id = 0)
    {
        $articles = $this->articlesModel->getAll();
        $c_articles = count($articles);

        if ($c_articles > 0) {
            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['selected'] = $this->formsHelper->selectEntry('articles', $articles[$i]['id'], $id);
            }
        }
        return $articles;
    }

    /**
     * @param $id
     * @return bool
     */
    public function articleExists($id)
    {
        return $this->articlesModel->resultExists($id);
    }

}