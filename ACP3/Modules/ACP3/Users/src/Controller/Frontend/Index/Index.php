<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\UserListViewProvider
     */
    private $userListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Users\ViewProviders\UserListViewProvider $userListViewProvider
    ) {
        parent::__construct($context);

        $this->userListViewProvider = $userListViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        try {
            return ($this->userListViewProvider)();
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
