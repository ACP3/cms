<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Sidebar\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Articles\Controller\Sidebar\Index
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    protected $articleRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\Context               $context
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache                   $articlesCache
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Core\Date $date,
        Articles\Model\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articlesCache = $articlesCache;
    }

    /**
     * @param string $template
     */
    public function execute($template = '')
    {
        $this->view->assign('sidebar_articles', $this->articleRepository->getAll($this->date->getCurrentDateTime(), 5));

        $this->setTemplate($template !== '' ? $template : 'Articles/Sidebar/index.index.tpl');
    }
}