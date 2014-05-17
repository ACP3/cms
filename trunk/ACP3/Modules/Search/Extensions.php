<?php

namespace ACP3\Modules\Search;

use ACP3\Core;

/**
 * Description of Extensions
 *
 * @author Tino Goratsch
 */
class Extensions
{

    /**
     *
     * @var string
     */
    protected $area;

    /**
     * Whther to sort the results ascending/descending
     *
     * @var string
     */
    protected $sort;

    /**
     * The search term
     *
     * @var string
     */
    protected $search_term;

    /**
     * DB Connection Handler
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * SQL Prepared Parameters
     *
     * @var array
     */
    protected $params = array();

    public function __construct($area, $sort, $search_term)
    {
        $this->area = $area;
        $this->sort = $sort;
        $this->search_term = $search_term;

        $this->db = Core\Registry::get('Db');

        $this->params = array(
            'searchterm' => $this->search_term,
            'time' => Core\Registry::get('Date')->getCurrentDateTime()
        );
    }

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

        $period = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
        $results = $this->db->fetchAll('SELECT id, title, text FROM ' . DB_PRE . 'articles WHERE MATCH (' . $fields . ') AGAINST (:searchterm IN BOOLEAN MODE) AND ' . $period . ' ORDER BY start ' . $this->sort . ', end ' . $this->sort . ', title ' . $this->sort, $this->params);
        $c_results = count($results);
        $search_results = array();

        if ($c_results > 0) {
            $name = Core\Registry::get('Lang')->t('articles', 'articles');
            $search_results[$name]['dir'] = 'articles';
            for ($i = 0; $i < $c_results; ++$i) {
                $search_results[$name]['results'][$i]['hyperlink'] = Core\Registry::get('URI')->route('articles/index/details/id_' . $results[$i]['id']);
                $search_results[$name]['results'][$i]['title'] = $results[$i]['title'];
                $search_results[$name]['results'][$i]['text'] = Core\Functions::shortenEntry($results[$i]['text'], 200, 0, '...');
            }
        }
        return $search_results;
    }

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

        $period = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
        $results = $this->db->fetchAll('SELECT id, title, text FROM ' . DB_PRE . 'files WHERE MATCH (' . $fields . ') AGAINST (:searchterm IN BOOLEAN MODE) AND ' . $period . ' ORDER BY start ' . $this->sort . ', end ' . $this->sort . ', id ' . $this->sort, $this->params);
        $c_results = count($results);
        $search_results = array();

        if ($c_results > 0) {
            $name = Core\Registry::get('Lang')->t('files', 'files');
            $search_results[$name]['dir'] = 'files';
            for ($i = 0; $i < $c_results; ++$i) {
                $search_results[$name]['results'][$i]['hyperlink'] = Core\Registry::get('URI')->route('files/index/details/id_' . $results[$i]['id']);
                $search_results[$name]['results'][$i]['title'] = $results[$i]['title'];
                $search_results[$name]['results'][$i]['text'] = Core\Functions::shortenEntry($results[$i]['text'], 200, 0, '...');
            }
        }
        return $search_results;
    }

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

        $period = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
        $results = $this->db->fetchAll('SELECT id, title, text FROM ' . DB_PRE . 'news WHERE MATCH (' . $fields . ') AGAINST (:searchterm IN BOOLEAN MODE) AND ' . $period . ' ORDER BY start ' . $this->sort . ', end ' . $this->sort . ', id ' . $this->sort, $this->params);
        $c_results = count($results);
        $search_results = array();

        if ($c_results > 0) {
            $name = Core\Registry::get('Lang')->t('news', 'news');
            $search_results[$name]['dir'] = 'news';
            for ($i = 0; $i < $c_results; ++$i) {
                $search_results[$name]['results'][$i]['hyperlink'] = Core\Registry::get('URI')->route('news/index/details/id_' . $results[$i]['id']);
                $search_results[$name]['results'][$i]['title'] = $results[$i]['title'];
                $search_results[$name]['results'][$i]['text'] = Core\Functions::shortenEntry($results[$i]['text'], 200, 0, '...');
            }
        }
        return $search_results;
    }

}
