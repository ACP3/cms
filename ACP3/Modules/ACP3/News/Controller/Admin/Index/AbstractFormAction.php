<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AbstractAdminAction
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
     * @param \ACP3\Core\Helpers\Forms                   $formsHelper
     * @param \ACP3\Modules\ACP3\Categories\Helpers      $categoriesHelpers
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
     * @param array $formData
     *
     * @return int
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
     * @return array
     */
    protected function fetchOptions($readMoreValue, $commentsValue)
    {
        $settings = $this->config->getSettings(News\Installer\Schema::MODULE_NAME);
        $options = [];
        if ($settings['readmore'] == 1) {
            $readMore = [
                '1' => $this->translator->t('news', 'activate_readmore')
            ];

            $options = $this->formsHelper->checkboxGenerator('readmore', $readMore, $readMoreValue);
        }
        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $comments = [
                '1' => $this->translator->t('system', 'allow_comments')
            ];

            $options = array_merge(
                $options,
                $this->formsHelper->checkboxGenerator('comments', $comments, $commentsValue)
            );
        }

        return $options;
    }
}
