<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;


use ACP3\Core\Controller\AdminAction;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class AbstractFormAction extends AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFieldsHelper;

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields $metaFormFieldsHelper
     */
    public function setMetaFormFieldsHelper(MetaFormFields $metaFormFieldsHelper)
    {
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
    }
}
