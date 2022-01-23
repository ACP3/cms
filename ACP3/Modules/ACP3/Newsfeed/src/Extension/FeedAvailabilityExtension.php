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
use ACP3\Modules\ACP3\News\Repository\NewsRepository;

class FeedAvailabilityExtension implements FeedAvailabilityExtensionInterface
{
    public function __construct(private Date $date, private RouterInterface $router, private StringFormatter $formatter, private NewsRepository $newsRepository)
    {
    }

    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchFeedItems(): array
    {
        $items = [];
        $results = $this->newsRepository->getAll($this->date->getCurrentDateTime(), 10);

        foreach ($results as $result) {
            $items[] = [
                'title' => $result['title'],
                'date' => $this->date->timestamp($result['start']),
                'description' => $this->formatter->shortenEntry($result['text'], 300, 0),
                'link' => $this->router->route('news/index/details/id_' . $result['id'], true),
            ];
        }

        return $items;
    }
}
