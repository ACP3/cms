<?php
namespace ACP3\Modules\ACP3\Articles;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles\Model\ArticleRepository;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Articles
 */
class Helpers
{
    const URL_KEY_PATTERN = 'articles/index/details/id_%s/';
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * @param Core\Helpers\Forms $formsHelper
     * @param ArticleRepository  $articleRepository
     */
    public function __construct(
        Core\Helpers\Forms $formsHelper,
        ArticleRepository $articleRepository
    )
    {
        $this->formsHelper = $formsHelper;
        $this->articleRepository = $articleRepository;
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
        $articles = $this->articleRepository->getAll();
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
     *
     * @return bool
     */
    public function articleExists($id)
    {
        return $this->articleRepository->resultExists($id);
    }
}
