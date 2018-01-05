<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param array $formData
     *
     * @return int
     */
    protected function fetchCategoryIdForSave(array $formData): int
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoryCreate($formData['cat_create'], News\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }
}
