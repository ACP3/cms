<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;

abstract class AbstractFormAction extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\Forms $formsHelper,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
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
            ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], Files\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }

    /**
     * @param array $formData
     *
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
     * @param array $file
     *
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
