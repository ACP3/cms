<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\PollListViewProvider
     */
    private $pollListViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Polls\ViewProviders\PollListViewProvider $pollListViewProvider
    ) {
        parent::__construct($context);

        $this->pollListViewProvider = $pollListViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): Response
    {
        $response = $this->renderTemplate(null, ($this->pollListViewProvider)());
        $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        return $response;
    }
}
