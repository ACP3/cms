<?php

namespace ACP3\Modules\Search;

use ACP3\Core;
use ACP3\Modules\Articles;
use ACP3\Modules\Files;
use ACP3\Modules\News;

/**
 * Class Extensions
 * @package ACP3\Modules\Search
 */
class Extensions
{
    /**
     *
     * @var string
     */
    protected $area;
    /**
     * Whether to sort the results ascending/descending
     *
     * @var string
     */
    protected $sort;
    /**
     * The search term
     *
     * @var string
     */
    protected $searchTerm;
    /**
     * The current datetime
     *
     * @var string
     */
    protected $time;

    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Modules\Articles\Model
     */
    protected $articlesModel;
    /**
     * @var \ACP3\Modules\Files\Model
     */
    protected $filesModel;
    /**
     * @var \ACP3\Modules\News\Model
     */
    protected $newsModel;

    /**
     * @param Core\Date $date
     * @param Core\Lang $lang
     * @param Core\Router $router
     */
    public function __construct(
        Core\Date $date,
        Core\Lang $lang,
        Core\Router $router
    ) {
        $this->lang = $lang;
        $this->router = $router;

        $this->time = $date->getCurrentDateTime();
    }

    /**
     * @param \ACP3\Modules\Articles\Model $articlesModel
     *
     * @return $this
     */
    public function setArticlesModel(Articles\Model $articlesModel)
    {
        $this->articlesModel = $articlesModel;

        return $this;
    }

    /**
     * @param \ACP3\Modules\Files\Model $filesModel
     *
     * @return $this
     */
    public function setFilesModel(Files\Model $filesModel)
    {
        $this->filesModel = $filesModel;

        return $this;
    }

    /**
     * @param \ACP3\Modules\News\Model $newsModel
     *
     * @return $this
     */
    public function setNewsModel(News\Model $newsModel)
    {
        $this->newsModel = $newsModel;

        return $this;
    }

    /**
     * @param $area
     *
     * @return $this
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @param $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param $searchTerm
     *
     * @return $this
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    /**
     * @return array
     */
    public function articlesSearch()
    {
        $searchResults = [];

        if ($this->filesModel) {
            switch ($this->area) {
                case 'title':
                    $fields = 'title';
                    break;
                case 'content':
                    $fields = 'text';
                    break;
                default:
                    $fields = 'title, text';
            }

            $results = $this->articlesModel->getAllSearchResults($fields, $this->searchTerm, $this->sort, $this->time);
            $c_results = count($results);

            if ($c_results > 0) {
                $name = $this->lang->t('articles', 'articles');
                $searchResults[$name]['dir'] = 'articles';
                for ($i = 0; $i < $c_results; ++$i) {
                    $searchResults[$name]['results'][$i] = $results[$i];

                    $searchResults[$name]['results'][$i]['hyperlink'] = $this->router->route('articles/index/details/id_' . $results[$i]['id']);
                }
            }
        }

        return $searchResults;
    }

    /**
     * @return array
     */
    public function filesSearch()
    {
        $searchResults = [];

        if ($this->filesModel) {
            switch ($this->area) {
                case 'title':
                    $fields = 'title, file';
                    break;
                case 'content':
                    $fields = 'text';
                    break;
                default:
                    $fields = 'title, file, text';
            }

            $results = $this->filesModel->getAllSearchResults($fields, $this->searchTerm, $this->sort, $this->time);
            $c_results = count($results);

            if ($c_results > 0) {
                $name = $this->lang->t('files', 'files');
                $searchResults[$name]['dir'] = 'files';
                for ($i = 0; $i < $c_results; ++$i) {
                    $searchResults[$name]['results'][$i] = $results[$i];

                    $searchResults[$name]['results'][$i]['hyperlink'] = $this->router->route('files/index/details/id_' . $results[$i]['id']);
                }
            }
        }

        return $searchResults;
    }

    /**
     * @return array
     */
    public function newsSearch()
    {
        $searchResults = [];

        if ($this->newsModel) {
            switch ($this->area) {
                case 'title':
                    $fields = 'title';
                    break;
                case 'content':
                    $fields = 'text';
                    break;
                default:
                    $fields = 'title, text';
            }

            $results = $this->newsModel->getAllSearchResults($fields, $this->searchTerm, $this->sort, $this->time);
            $c_results = count($results);

            if ($c_results > 0) {
                $name = $this->lang->t('news', 'news');
                $searchResults[$name]['dir'] = 'news';
                for ($i = 0; $i < $c_results; ++$i) {
                    $searchResults[$name]['results'][$i] = $results[$i];

                    $searchResults[$name]['results'][$i]['hyperlink'] = $this->router->route('news/index/details/id_' . $results[$i]['id']);
                }
            }
        }

        return $searchResults;
    }
}
