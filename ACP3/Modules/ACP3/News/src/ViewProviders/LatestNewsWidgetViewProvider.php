<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;

class LatestNewsWidgetViewProvider
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    private $newsRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        Date $date,
        NewsRepository $newsRepository,
        SettingsInterface $settings)
    {
        $this->date = $date;
        $this->newsRepository = $newsRepository;
        $this->settings = $settings;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(?int $categoryId): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($categoryId) {
            $news = $this->newsRepository->getLatestByCategoryId($categoryId, $this->date->getCurrentDateTime());
        } else {
            $news = $this->newsRepository->getLatest($this->date->getCurrentDateTime());
        }

        return [
            'sidebar_news_latest' => $news,
            'dateformat' => $settings['dateformat'],
        ];
    }
}
