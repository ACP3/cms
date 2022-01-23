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
    public function __construct(private FormToken $formTokenHelper, private MetaFormFields $metaFormFieldsHelper, private Request $request, private Title $title)
    {
    }

    /**
     * @param array<string, mixed> $seo
     *
     * @return array<string, mixed>
     */
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
