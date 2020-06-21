<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;

abstract class AbstractFormAction extends AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    private $categoriesHelpers;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function fetchCategoryId(array $formData): int
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], Files\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }
}
