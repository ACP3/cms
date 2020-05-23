<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Widget\Index;

use ACP3\Core\Cache\CacheResponseTrait;
use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Share\ViewProviders\ShareWidgetViewProvider;

class Index extends AbstractWidgetAction
{
    use CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Share\ViewProviders\ShareWidgetViewProvider
     */
    private $shareWidgetViewProvider;

    public function __construct(
        WidgetContext $context,
        ShareWidgetViewProvider $shareWidgetViewProvider
    ) {
        parent::__construct($context);

        $this->shareWidgetViewProvider = $shareWidgetViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(string $path, string $template = ''): array
    {
        $this->setCacheResponseCacheable(3600);
        $this->setTemplate($template);

        return ($this->shareWidgetViewProvider)($path);
    }
}
