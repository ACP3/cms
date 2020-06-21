<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Create extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\ViewProviders\AdminCategoryEditViewProvider
     */
    private $adminCategoryEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Categories\ViewProviders\AdminCategoryEditViewProvider $adminCategoryEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminCategoryEditViewProvider = $adminCategoryEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        return ($this->adminCategoryEditViewProvider)($this->getDefaultFormData());
    }

    private function getDefaultFormData(): array
    {
        return [
            'parent_id' => 0,
            'title' => '',
            'description' => '',
        ];
    }
}
