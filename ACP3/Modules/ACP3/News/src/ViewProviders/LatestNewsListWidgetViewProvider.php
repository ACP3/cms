<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\News\Installer\Schema as NewsSchema;
use ACP3\Modules\ACP3\News\Repository\NewsRepository;

class LatestNewsListWidgetViewProvider
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\News\Repository\NewsRepository
     */
    private $newsRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        Date $date,
        NewsRepository $newsRepository,
        SettingsInterface $settings
    ) {
        $this->date = $date;
        $this->newsRepository = $newsRepository;
        $this->settings = $settings;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(?int $categoryId): array
    {
        $settings = $this->settings->getSettings(NewsSchema::MODULE_NAME);

        return [
            'sidebar_news' => $this->fetchNews($categoryId, $settings),
            'dateformat' => $settings['dateformat'],
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchNews(int $categoryId, array $settings): array
    {
        if (!empty($categoryId)) {
            $news = $this->newsRepository->getAllByCategoryId(
                $categoryId,
                $this->date->getCurrentDateTime(),
                $settings['sidebar']
            );
        } else {
            $news = $this->newsRepository->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }

        return $news;
    }
}
