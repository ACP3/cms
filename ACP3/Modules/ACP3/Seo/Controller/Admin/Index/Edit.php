<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin\Index
 */
class Edit extends Core\Controller\AbstractAdminAction
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

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields $metaFormFieldsHelper
     * @param Seo\Model\SeoModel $seoModel
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
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
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $seo = $this->seoModel->getOneById($id);

        if (empty($seo) === false) {
            $this->title->setPageTitlePostfix($seo['alias']);

            return [
                'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper->formFields($seo['uri']),
                'form' => array_merge(['uri' => $seo['uri']], $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int    $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
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
