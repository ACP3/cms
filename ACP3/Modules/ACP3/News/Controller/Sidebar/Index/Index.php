<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Sidebar\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\News\Controller\Sidebar\Index
 */
class Index extends Core\Modules\Controller
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
     * @param \ACP3\Core\Modules\Controller\Context        $context
     * @param \ACP3\Core\Date                              $date
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository $newsRepository
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Date $date,
        News\Model\NewsRepository $newsRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param int    $categoryId
     * @param string $template
     */
    public function execute($categoryId = 0, $template = '')
    {
        $settings = $this->config->getSettings('news');

        if (!empty($categoryId)) {
            $news = $this->newsRepository->getAllByCategoryId((int)$categoryId, $this->date->getCurrentDateTime(), $settings['sidebar']);
        } else {
            $news = $this->newsRepository->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }
        $this->view->assign('sidebar_news', $news);
        $this->view->assign('dateformat', $settings['dateformat']);

        $this->setTemplate($template !== '' ? $template : 'News/Sidebar/index.index.tpl');
    }
}
