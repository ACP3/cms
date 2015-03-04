<?php

namespace ACP3\Modules\ACP3\Feeds;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\News;

/**
 * Class Extensions
 * @package ACP3\Modules\ACP3\Feeds
 */
class Extensions
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var Core\Helpers\StringFormatter
     */
    protected $formatter;
    /**
     * @var \ACP3\Modules\ACP3\News\Model
     */
    protected $newsModel;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model
     */
    protected $filesModel;

    /**
     * @param \ACP3\Core\Date                    $date
     * @param \ACP3\Core\Router                  $router
     * @param \ACP3\Core\Helpers\StringFormatter $stringFormatter
     */
    public function __construct(
        Core\Date $date,
        Core\Router $router,
        Core\Helpers\StringFormatter $stringFormatter
    )
    {
        $this->date = $date;
        $this->router = $router;
        $this->formatter = $stringFormatter;
    }

    /**
     * @param \ACP3\Modules\ACP3\News\Model $newsModel
     *
     * @return $this
     */
    public function setNewsModel(News\Model $newsModel)
    {
        $this->newsModel = $newsModel;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Files\Model $filesModel
     *
     * @return $this
     */
    public function setFilesModel(Files\Model $filesModel)
    {
        $this->filesModel = $filesModel;

        return $this;
    }

    /**
     * @return array
     */
    public function newsFeed()
    {
        $items = [];
        if ($this->newsModel) {
            $results = $this->newsModel->getAll($this->date->getCurrentDateTime(), 10);
            $c_results = count($results);

            for ($i = 0; $i < $c_results; ++$i) {
                $items[] = [
                    'title' => $results[$i]['title'],
                    'date' => $this->date->timestamp($results[$i]['start']),
                    'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                    'link' => $this->router->route('news/index/details/id_' . $results[$i]['id'], true)
                ];
            }
        }

        return $items;
    }

    /**
     * @return array
     */
    public function filesFeed()
    {
        $items = [];
        if ($this->filesModel) {
            $results = $this->filesModel->getAll($this->date->getCurrentDateTime(), 10);
            $c_results = count($results);

            for ($i = 0; $i < $c_results; ++$i) {
                $items[] = [
                    'title' => $results[$i]['title'],
                    'date' => $this->date->timestamp($results[$i]['start']),
                    'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                    'link' => $this->router->route('files/index/details/id_' . $results[$i]['id'], true)
                ];
            }
        }

        return $items;
    }
}
