<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\News;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\News\ViewProviders\NewsListViewProvider
     */
    private $newsListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        News\ViewProviders\NewsListViewProvider $newsListViewProvider
    ) {
        parent::__construct($context);

        $this->newsListViewProvider = $newsListViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $cat = 0): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        try {
            return ($this->newsListViewProvider)($cat);
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
