<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Guestbook\ViewProviders\GuestbookListViewProvider
     */
    private $guestbookListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Guestbook\ViewProviders\GuestbookListViewProvider $guestbookListViewProvider
    ) {
        parent::__construct($context);

        $this->guestbookListViewProvider = $guestbookListViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        try {
            return ($this->guestbookListViewProvider)();
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
