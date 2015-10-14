<?php
namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\Date;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Router;
use ACP3\Modules\ACP3\Feeds\Event\DisplayFeed;
use ACP3\Modules\ACP3\News\Model;

/**
 * Class OnDisplayFeedListener
 * @package ACP3\Modules\ACP3\News\Event\Listener
 */
class OnDisplayFeedListener
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
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $formatter;
    /**
     * @var \ACP3\Modules\ACP3\News\Model
     */
    protected $newsModel;

    /**
     * @param \ACP3\Core\Date                    $date
     * @param \ACP3\Core\Router                  $router
     * @param \ACP3\Core\Helpers\StringFormatter $formatter
     * @param \ACP3\Modules\ACP3\News\Model      $newsModel
     */
    public function __construct(
        Date $date,
        Router $router,
        StringFormatter $formatter,
        Model $newsModel
    )
    {
        $this->date = $date;
        $this->router = $router;
        $this->formatter = $formatter;
        $this->newsModel = $newsModel;
    }

    /**
     * @param \ACP3\Modules\ACP3\Feeds\Event\DisplayFeed $displayFeed
     */
    public function onDisplayFeed(DisplayFeed $displayFeed)
    {
        $items = [];
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

        $displayFeed->getView()->assign($items);
    }
}