<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files\ViewProviders\RootCategoriesListViewProvider;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Files\ViewProviders\RootCategoriesListViewProvider
     */
    private $rootCategoriesListViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        RootCategoriesListViewProvider $rootCategoriesListViewProvider
    ) {
        parent::__construct($context);

        $this->rootCategoriesListViewProvider = $rootCategoriesListViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): Response
    {
        $response = $this->renderTemplate(null, ($this->rootCategoriesListViewProvider)());
        $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        return $response;
    }
}
