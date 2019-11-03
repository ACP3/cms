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

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\Forms                      $formsHelper
     * @param \ACP3\Modules\ACP3\Categories\Helpers         $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @return int
     */
    protected function fetchCategoryId(array $formData)
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], Files\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }

    /**
     * @return int
     */
    protected function useComments(array $formData)
    {
        $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }

    /**
     * @return array
     */
    protected function getUnits()
    {
        return [
            'Byte' => 'Byte',
            'KiB' => 'KiB',
            'MiB' => 'MiB',
            'GiB' => 'GiB',
            'TiB' => 'TiB',
        ];
    }

    /**
     * @return array
     */
    protected function getOptions(array $file)
    {
        $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

        $options = [];
        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $comments = [
                '1' => $this->translator->t('system', 'allow_comments'),
            ];

            $options = $this->formsHelper->checkboxGenerator('comments', $comments, $file['comments']);
        }

        return $options;
    }
}
