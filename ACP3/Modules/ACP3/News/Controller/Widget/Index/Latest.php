<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

/**
 * Class Latest
 * @package ACP3\Modules\ACP3\News\Controller\Widget\Index
 */
class Latest extends Core\Controller\WidgetController
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    protected $newsRepository;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext  $context
     * @param \ACP3\Core\Date                              $date
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository $newsRepository
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        News\Model\NewsRepository $newsRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param int $categoryId
     */
    public function execute($categoryId = 0)
    {
        $settings = $this->config->getSettings('news');

        if (!empty($categoryId)) {
            $news = $this->newsRepository->getLatestByCategoryId((int)$categoryId, $this->date->getCurrentDateTime());
        } else {
            $news = $this->newsRepository->getLatest($this->date->getCurrentDateTime());
        }

        $this->view->assign('sidebar_news_latest', $news);
        $this->view->assign('dateformat', $settings['dateformat']);

        $this->setTemplate('News/Widget/index.latest.tpl');
    }
}
