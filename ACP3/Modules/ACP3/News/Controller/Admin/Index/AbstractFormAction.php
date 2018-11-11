<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

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
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function fetchCategoryIdForSave(array $formData)
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], News\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }

    /**
     * @param int $readMoreValue
     * @param int $commentsValue
     *
     * @return array
     */
    protected function fetchOptions(int $readMoreValue, int $commentsValue)
    {
        $settings = $this->config->getSettings(News\Installer\Schema::MODULE_NAME);
        $options = [];
        if ($settings['readmore'] == 1) {
            $readMore = [
                '1' => $this->translator->t('news', 'activate_readmore'),
            ];

            $options = $this->formsHelper->checkboxGenerator('readmore', $readMore, $readMoreValue);
        }
        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $comments = [
                '1' => $this->translator->t('system', 'allow_comments'),
            ];

            $options = \array_merge(
                $options,
                $this->formsHelper->checkboxGenerator('comments', $comments, $commentsValue)
            );
        }

        return $options;
    }
}
