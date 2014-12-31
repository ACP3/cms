<?php

namespace ACP3\Modules\Search;

use ACP3\Core;
use Symfony\Component\DependencyInjection\Container;

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
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;

    /**
     * @param Container $container
     * @param Core\Date $date
     * @param Core\Lang $lang
     * @param Core\Router $router
     */
    public function __construct(
        Container $container,
        Core\Date $date,
        Core\Lang $lang,
        Core\Router $router
    ) {
        $this->container = $container;
        $this->lang = $lang;
        $this->router = $router;

        $this->time = $date->getCurrentDateTime();
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

        $results = $this->container->get('articles.model')->getAllSearchResults($fields, $this->searchTerm, $this->sort, $this->time);
        $c_results = count($results);
        $searchResults = [];

        if ($c_results > 0) {
            $name = $this->lang->t('articles', 'articles');
            $searchResults[$name]['dir'] = 'articles';
            for ($i = 0; $i < $c_results; ++$i) {
                $searchResults[$name]['results'][$i] = $results[$i];

                $searchResults[$name]['results'][$i]['hyperlink'] = $this->router->route('articles/index/details/id_' . $results[$i]['id']);
            }
        }
        return $searchResults;
    }

    /**
     * @return array
     */
    public function filesSearch()
    {
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

        $results = $this->container->get('files.model')->getAllSearchResults($fields, $this->searchTerm, $this->sort, $this->time);
        $c_results = count($results);
        $searchResults = [];

        if ($c_results > 0) {
            $name = $this->lang->t('files', 'files');
            $searchResults[$name]['dir'] = 'files';
            for ($i = 0; $i < $c_results; ++$i) {
                $searchResults[$name]['results'][$i] = $results[$i];

                $searchResults[$name]['results'][$i]['hyperlink'] = $this->router->route('files/index/details/id_' . $results[$i]['id']);
            }
        }
        return $searchResults;
    }

    /**
     * @return array
     */
    public function newsSearch()
    {
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

        $results = $this->container->get('news.model')->getAllSearchResults($fields, $this->searchTerm, $this->sort, $this->time);
        $c_results = count($results);
        $searchResults = [];

        if ($c_results > 0) {
            $name = $this->lang->t('news', 'news');
            $searchResults[$name]['dir'] = 'news';
            for ($i = 0; $i < $c_results; ++$i) {
                $searchResults[$name]['results'][$i] = $results[$i];

                $searchResults[$name]['results'][$i]['hyperlink'] = $this->router->route('news/index/details/id_' . $results[$i]['id']);
            }
        }
        return $searchResults;
    }
}
