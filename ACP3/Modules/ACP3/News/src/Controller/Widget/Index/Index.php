<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\News\ViewProviders\LatestNewsListWidgetViewProvider
     */
    private $latestNewsListWidgetViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        News\ViewProviders\LatestNewsListWidgetViewProvider $latestNewsListWidgetViewProvider
    ) {
        parent::__construct($context);

        $this->latestNewsListWidgetViewProvider = $latestNewsListWidgetViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $categoryId = 0, string $template = ''): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $this->setTemplate($template);

        return ($this->latestNewsListWidgetViewProvider)($categoryId);
    }
}
