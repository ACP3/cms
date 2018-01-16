<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Widget\Index;

use ACP3\Core;

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
     * @var \ACP3\Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param \ACP3\Core\View\Block\BlockInterface        $block
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\View\Block\BlockInterface $block
    ) {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * @param string $template
     *
     * @return array
     */
    public function execute(string $template = '')
    {
        $this->setCacheResponseCacheable();

        return $this->block
            ->setTemplate($template)
            ->render();
    }
}
