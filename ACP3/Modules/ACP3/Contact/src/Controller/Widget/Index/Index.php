<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Contact\ViewProviders\ContactDetailsViewProvider;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Index extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Contact\ViewProviders\ContactDetailsViewProvider
     */
    private $contactDetailsViewProvider;

    public function __construct(
        WidgetContext $context,
        ContactDetailsViewProvider $contactDetailsViewProvider
    ) {
        parent::__construct($context);

        $this->contactDetailsViewProvider = $contactDetailsViewProvider;
    }

    public function execute(): Response
    {
        $response = $this->renderTemplate(null, ($this->contactDetailsViewProvider)());
        $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        return $response;
    }
}
