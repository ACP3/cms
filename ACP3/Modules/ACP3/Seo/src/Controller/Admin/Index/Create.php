<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

class Create extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\ViewProviders\AdminSeoEditViewProvider
     */
    private $adminSeoEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Seo\ViewProviders\AdminSeoEditViewProvider $adminSeoEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminSeoEditViewProvider = $adminSeoEditViewProvider;
    }

    public function __invoke(): array
    {
        $defaults = [
            'alias' => '',
            'uri' => '',
        ];

        return ($this->adminSeoEditViewProvider)($defaults);
    }
}
