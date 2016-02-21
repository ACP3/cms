<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin\Index
 */
class Create extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\SeoRepository
     */
    protected $seoRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation
     */
    protected $adminFormValidation;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext            $context
     * @param \ACP3\Core\Helpers\FormToken                          $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Model\SeoRepository            $seoRepository
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Model\SeoRepository $seoRepository,
        Seo\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->seoRepository = $seoRepository;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        return [
            'SEO_FORM_FIELDS' => $this->seo->formFields(),
            'form' => array_merge(['uri' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->adminFormValidation->validate($formData);

            $bool = $this->seo->insertUriAlias(
                $formData['uri'],
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}