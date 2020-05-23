<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\PollListViewProvider
     */
    private $pollListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Polls\ViewProviders\PollListViewProvider $pollListViewProvider
    ) {
        parent::__construct($context);

        $this->pollListViewProvider = $pollListViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        return ($this->pollListViewProvider)();
    }
}
