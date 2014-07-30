<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Description of FeedsExtensions
 *
 * @author Tino Goratsch
 */
class Extensions
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
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

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        Core\Date $date,
        Core\Router $router,
        Core\View $view,
        Core\Helpers\StringFormatter $stringFormatter
    )
    {
        $this->date = $date;
        $this->db = $db;
        $this->router = $router;
        $this->view = $view;
        $this->formatter = $stringFormatter;
    }

    public function newsFeed()
    {
        $results = $this->db->fetchAll('SELECT id, start, title, text FROM ' . DB_PRE . 'news WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end) ORDER BY start DESC, end DESC, id DESC LIMIT 10', array('time' => $this->date->getCurrentDateTime()));
        $c_results = count($results);

        for ($i = 0; $i < $c_results; ++$i) {
            $params = array(
                'title' => $results[$i]['title'],
                'date' => $this->date->timestamp($results[$i]['start']),
                'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                'link' => FEED_LINK . $this->router->route('news/index/details/id_' . $results[$i]['id'])
            );
            $this->view->assign($params);
        }
    }

    public function filesFeed()
    {
        $results = $this->db->fetchAll('SELECT id, start, title, text FROM ' . DB_PRE . 'files WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end) ORDER BY start DESC, end DESC, id DESC LIMIT 10', array('time' => $this->date->getCurrentDateTime()));
        $c_results = count($results);

        for ($i = 0; $i < $c_results; ++$i) {
            $params = array(
                'title' => $results[$i]['title'],
                'date' => $this->date->timestamp($results[$i]['start']),
                'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                'link' => FEED_LINK . $this->router->route('files/index/details/id_' . $results[$i]['id'])
            );
            $this->view->assign($params);
        }
    }

}