<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filesfeed\Extension;

use ACP3\Core\Date;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Feeds\Extension\FeedAvailabilityExtensionInterface;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Repository\FilesRepository;

class FeedAvailabilityExtension implements FeedAvailabilityExtensionInterface
{
    public function __construct(private Date $date, private RouterInterface $router, private StringFormatter $formatter, private FilesRepository $filesRepository)
    {
    }

    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchFeedItems(): array
    {
        $items = [];
        $results = $this->filesRepository->getAll($this->date->getCurrentDateTime(), 10);

        foreach ($results as $result) {
            $items[] = [
                'title' => $result['title'],
                'date' => $this->date->timestamp($result['start']),
                'description' => $this->formatter->shortenEntry($result['text'], 300, 0),
                'link' => $this->router->route('files/index/details/id_' . $result['id'], true),
            ];
        }

        return $items;
    }
}
