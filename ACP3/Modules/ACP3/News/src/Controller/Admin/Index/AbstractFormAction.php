<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

abstract class AbstractFormAction extends AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchCategoryIdForSave(array $formData): int
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], News\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }
}
