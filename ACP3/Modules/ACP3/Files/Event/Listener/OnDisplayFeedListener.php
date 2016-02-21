<?php
namespace ACP3\Modules\ACP3\Files\Event\Listener;

use ACP3\Core\Date;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Router;
use ACP3\Modules\ACP3\Feeds\Event\DisplayFeed;
use ACP3\Modules\ACP3\Files\Model\FilesRepository;

/**
 * Class OnDisplayFeedListener
 * @package ACP3\Modules\ACP3\Files\Event\Listener
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
     * @var \ACP3\Modules\ACP3\Files\Model\FilesRepository
     */
    protected $filesRepository;

    /**
     * @param \ACP3\Core\Date                                $date
     * @param \ACP3\Core\Router                              $router
     * @param \ACP3\Core\Helpers\StringFormatter             $formatter
     * @param \ACP3\Modules\ACP3\Files\Model\FilesRepository $filesRepository
     */
    public function __construct(
        Date $date,
        Router $router,
        StringFormatter $formatter,
        FilesRepository $filesRepository
    )
    {
        $this->date = $date;
        $this->router = $router;
        $this->formatter = $formatter;
        $this->filesRepository = $filesRepository;
    }

    /**
     * @param \ACP3\Modules\ACP3\Feeds\Event\DisplayFeed $displayFeed
     */
    public function onDisplayFeed(DisplayFeed $displayFeed)
    {
        $items = [];
        $results = $this->filesRepository->getAll($this->date->getCurrentDateTime(), 10);
        $cResults = count($results);

        for ($i = 0; $i < $cResults; ++$i) {
            $items[] = [
                'title' => $results[$i]['title'],
                'date' => $this->date->timestamp($results[$i]['start']),
                'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                'link' => $this->router->route('files/index/details/id_' . $results[$i]['id'], true)
            ];
        }

        $displayFeed->getFeedGenerator()->assign($items);
    }
}