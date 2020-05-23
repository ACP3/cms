<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Archive;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Newsletter;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterArchiveViewProvider
     */
    private $newsletterArchiveViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Newsletter\ViewProviders\NewsletterArchiveViewProvider $newsletterArchiveViewProvider
    ) {
        parent::__construct($context);

        $this->newsletterArchiveViewProvider = $newsletterArchiveViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        try {
            return ($this->newsletterArchiveViewProvider)();
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
