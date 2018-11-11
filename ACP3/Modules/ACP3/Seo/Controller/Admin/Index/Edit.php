<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

class Edit extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFieldsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Seo\Model\SeoModel
     */
    protected $seoModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Helper\MetaFormFields $metaFormFieldsHelper,
        Seo\Model\SeoModel $seoModel,
        Seo\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->seoModel = $seoModel;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $seo = $this->seoModel->getOneById($id);

        if (empty($seo) === false) {
            $this->title->setPageTitlePrefix($seo['alias']);

            return [
                'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper->formFields($seo['uri']),
                'form' => \array_merge(['uri' => $seo['uri']], $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $seo = $this->seoModel->getOneById($id);

            $this->adminFormValidation
                ->setUriAlias($seo['uri'])
                ->validate($formData);

            return $this->seoModel->save($formData, $id);
        });
    }
}
