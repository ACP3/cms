<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsfeed\Extension;

use ACP3\Core\Date;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Feeds\Extension\FeedAvailabilityExtensionInterface;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;

class FeedAvailabilityExtension implements FeedAvailabilityExtensionInterface
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    private $formatter;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    private $newsRepository;

    public function __construct(
        Date $date,
        RouterInterface $router,
        StringFormatter $formatter,
        NewsRepository $newsRepository
    ) {
        $this->date = $date;
        $this->router = $router;
        $this->formatter = $formatter;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @return array
     */
    public function fetchFeedItems()
    {
        $items = [];
        $results = $this->newsRepository->getAll($this->date->getCurrentDateTime(), 10);
        $cResults = \count($results);

        for ($i = 0; $i < $cResults; ++$i) {
            $items[] = [
                'title' => $results[$i]['title'],
                'date' => $this->date->timestamp($results[$i]['start']),
                'description' => $this->formatter->shortenEntry($results[$i]['text'], 300, 0),
                'link' => $this->router->route('news/index/details/id_' . $results[$i]['id'], true),
            ];
        }

        return $items;
    }
}
