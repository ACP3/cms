<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AdminAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Files\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFieldsHelper;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Modules\ACP3\Categories\Helpers      $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields $metaFormFieldsHelper
     */
    public function setMetaFormFieldsHelper(MetaFormFields $metaFormFieldsHelper)
    {
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
    }

    /**
     * @param array $formData
     *
     * @return int
     */
    protected function fetchCategoryId(array $formData)
    {
        return !empty($formData['cat_create']) ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'],
            'files') : $formData['cat'];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useComments(array $formData, array $settings)
    {
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
            'TiB' => 'TiB'
        ];
    }
}
