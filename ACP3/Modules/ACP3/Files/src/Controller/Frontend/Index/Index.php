<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files\ViewProviders\RootCategoriesListViewProvider;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Files\ViewProviders\RootCategoriesListViewProvider
     */
    private $rootCategoriesListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        RootCategoriesListViewProvider $rootCategoriesListViewProvider
    ) {
        parent::__construct($context);

        $this->rootCategoriesListViewProvider = $rootCategoriesListViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable(
            $this->config->getSettings(\ACP3\Modules\ACP3\System\Installer\Schema::MODULE_NAME)['cache_lifetime']
        );

        return ($this->rootCategoriesListViewProvider)();
    }
}
