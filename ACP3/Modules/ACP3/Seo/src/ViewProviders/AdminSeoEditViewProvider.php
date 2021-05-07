<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\Request;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;

class AdminSeoEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    private $metaFormFieldsHelper;
    /**
     * @var \ACP3\Core\Http\Request
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;

    public function __construct(
        FormToken $formTokenHelper,
        MetaFormFields $metaFormFieldsHelper,
        Request $request,
        Title $title
    ) {
        $this->formTokenHelper = $formTokenHelper;
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
        $this->request = $request;
        $this->title = $title;
    }

    public function __invoke(array $seo): array
    {
        $this->title->setPageTitlePrefix($seo['alias']);

        return [
            'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper->formFields($seo['uri']),
            'form' => array_merge(['uri' => $seo['uri']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
