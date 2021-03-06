<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

class Latest extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\News\ViewProviders\LatestNewsWidgetViewProvider
     */
    private $latestNewsWidgetViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        News\ViewProviders\LatestNewsWidgetViewProvider $latestNewsWidgetViewProvider
    ) {
        parent::__construct($context);

        $this->latestNewsWidgetViewProvider = $latestNewsWidgetViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(int $categoryId = 0): array
    {
        return ($this->latestNewsWidgetViewProvider)($categoryId);
    }
}
