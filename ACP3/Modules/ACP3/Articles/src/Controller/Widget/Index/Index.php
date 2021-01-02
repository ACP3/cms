<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Index extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Articles\ViewProviders\LatestArticlesViewProvider
     */
    private $latestArticlesViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Articles\ViewProviders\LatestArticlesViewProvider $latestArticlesViewProvider
    ) {
        parent::__construct($context);

        $this->latestArticlesViewProvider = $latestArticlesViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(string $template = ''): Response
    {
        $response = $this->renderTemplate($template, ($this->latestArticlesViewProvider)());
        $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        return $response;
    }
}
