<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;

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
    protected function fetchCategoryId(array $formData)
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoryCreate($formData['cat_create'], Files\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }

    /**
     * @param array $formData
     * @return int
     */
    protected function useComments(array $formData)
    {
        $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }
}
