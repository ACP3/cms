<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\View\Block\Admin;


use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;

class SeoFormBlock extends AbstractFormBlock
{
    /**
     * @var MetaFormFields
     */
    private $metaFormFields;

    /**
     * SeoFormBlock constructor.
     * @param FormBlockContext $context
     * @param MetaFormFields $metaFormFields
     */
    public function __construct(FormBlockContext $context, MetaFormFields $metaFormFields)
    {
        parent::__construct($context);

        $this->metaFormFields = $metaFormFields;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->title->setPageTitlePrefix($data['alias']);

        return [
            'SEO_FORM_FIELDS' => $this->metaFormFields->formFields($data['uri']),
            'form' => array_merge($data, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'alias' => '',
            'uri' => ''
        ];
    }
}
