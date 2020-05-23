<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Articles\ViewProviders\ArticleListViewProvider
     */
    private $articleListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Articles\ViewProviders\ArticleListViewProvider $articleListViewProvider
    ) {
        parent::__construct($context);

        $this->articleListViewProvider = $articleListViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        try {
            return ($this->articleListViewProvider)();
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
