<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Extensions
 * @package ACP3\Modules\Feeds
 */
class Extensions
{
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var Core\Helpers\StringFormatter
     */
    protected $formatter;

    /**
     * @param Container $container
     * @param Core\Date $date
     * @param Core\Router $router
     * @param Core\View $view
     * @param Core\Helpers\StringFormatter $stringFormatter
     */
    public function __construct(
        Container $container,
        Core\Date $date,
        Core\Router $router,
        Core\View $view,
        Core\Helpers\StringFormatter $stringFormatter
    )
    {
        $this->date = $date;
        $this->container = $container;
        $this->router = $router;
        $this->view = $view;
        $this->formatter = $stringFormatter;
    }

    public function newsFeed()
    {
        $results = $this->container->get('news.model')->getAll($this->date->getCurrentDateTime(), 10);
        $c_results = count($results);

        for ($i = 0; $i < $c_results; ++$i) {
            $params = array(
                'title' => $results[$i]['title'],
                'date' => $this->date->timestamp($results[$i]['start']),
                'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                'link' => $this->router->route('news/index/details/id_' . $results[$i]['id'], true)
            );
            $this->view->assign($params);
        }
    }

    public function filesFeed()
    {
        $results = $this->container->get('files.model')->getAll($this->date->getCurrentDateTime(), 10);
        $c_results = count($results);

        for ($i = 0; $i < $c_results; ++$i) {
            $params = array(
                'title' => $results[$i]['title'],
                'date' => $this->date->timestamp($results[$i]['start']),
                'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                'link' => $this->router->route('files/index/details/id_' . $results[$i]['id'], true)
            );
            $this->view->assign($params);
        }
    }

}