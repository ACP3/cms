<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\View\Block\Admin;

use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;
use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;

class SeoAdminFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var MetaFormFields
     */
    private $metaFormFields;

    /**
     * SeoFormBlock constructor.
     * @param FormBlockContext $context
     * @param SeoRepository $seoRepository
     * @param MetaFormFields $metaFormFields
     */
    public function __construct(
        FormBlockContext $context,
        SeoRepository $seoRepository,
        MetaFormFields $metaFormFields
    ) {
        parent::__construct($context, $seoRepository);

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
            'form' => \array_merge($data, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'alias' => '',
            'uri' => '',
        ];
    }
}
