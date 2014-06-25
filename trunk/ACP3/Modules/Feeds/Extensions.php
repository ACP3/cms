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
    private $db;
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\URI
     */
    private $uri;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    private $formatter;

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        Core\Date $date,
        Core\URI $uri,
        Core\View $view
    )
    {
        $this->date = $date;
        $this->db = $db;
        $this->uri = $uri;
        $this->view = $view;

        $this->formatter = new Core\Helpers\StringFormatter();
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
                'link' => FEED_LINK . $this->uri->route('news/index/details/id_' . $results[$i]['id'])
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
                'link' => FEED_LINK . $this->uri->route('files/index/details/id_' . $results[$i]['id'])
            );
            $this->view->assign($params);
        }
    }

}