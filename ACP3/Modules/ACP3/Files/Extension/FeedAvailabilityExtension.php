<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Extension;

use ACP3\Core\Date;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Feeds\Extension\FeedAvailabilityExtensionInterface;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class FeedAvailabilityExtension implements FeedAvailabilityExtensionInterface
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $formatter;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;

    /**
     * OnDisplayFeedListener constructor.
     *
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Router\RouterInterface $router
     * @param \ACP3\Core\Helpers\StringFormatter $formatter
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository $filesRepository
     */
    public function __construct(
        Date $date,
        RouterInterface $router,
        StringFormatter $formatter,
        FilesRepository $filesRepository
    ) {
        $this->date = $date;
        $this->router = $router;
        $this->formatter = $formatter;
        $this->filesRepository = $filesRepository;
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

        return $items;
    }
}
