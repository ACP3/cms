<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

class Index extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository
     */
    protected $articleRepository;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository $articleRepository
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Articles\Model\Repository\ArticlesRepository $articleRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param string $template
     * @return array
     */
    public function execute($template = '')
    {
        $this->setCacheResponseCacheable();

        $this->view->setTemplate($template);

        return [
            'sidebar_articles' => $this->articleRepository->getAll($this->date->getCurrentDateTime(), 5)
        ];
    }
}
