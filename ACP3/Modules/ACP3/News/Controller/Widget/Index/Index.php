<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\News;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\News\Controller\Widget\Index
 */
class Index extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var CategoriesRepository
     */
    private $categoriesRepository;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository $newsRepository
     * @param CategoriesRepository $categoriesRepository
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        News\Model\Repository\NewsRepository $newsRepository,
        CategoriesRepository $categoriesRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->newsRepository = $newsRepository;
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @param int    $categoryId
     * @param string $template
     *
     * @return array
     */
    public function execute($categoryId = 0, $template = '')
    {
        $this->setCacheResponseCacheable();

        $settings = $this->config->getSettings(News\Installer\Schema::MODULE_NAME);

        $this->view->setTemplate($template);

        return [
            'sidebar_news' => $this->fetchNews($categoryId, $settings),
            'dateformat' => $settings['dateformat']
        ];
    }

    /**
     * @param int $categoryId
     * @param array $settings
     * @return array
     */
    private function fetchNews($categoryId, array $settings)
    {
        if (!empty($categoryId)) {
            $news = $this->newsRepository->getAllByCategoryId(
                $this->categoriesRepository->getAllSiblingsAsId((int)$categoryId),
                $this->date->getCurrentDateTime(),
                $settings['sidebar']
            );
        } else {
            $news = $this->newsRepository->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }

        return $news;
    }
}
