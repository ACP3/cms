<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;


use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class AbstractFormAction extends AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFieldsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields $metaFormFieldsHelper
     */
    public function setMetaFormFieldsHelper(MetaFormFields $metaFormFieldsHelper)
    {
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @param array $formData
     * @param int   $galleryId
     */
    protected function insertUriAlias(array $formData, $galleryId)
    {
        if ($this->uriAliasManager) {
            $this->uriAliasManager->insertUriAlias(
                sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
        }
    }
}
