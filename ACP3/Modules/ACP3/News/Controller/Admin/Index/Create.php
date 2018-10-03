<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\News\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var News\Model\NewsModel
     */
    protected $newsModel;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext          $context
     * @param \ACP3\Core\Helpers\Forms                               $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                           $formTokenHelper
     * @param News\Model\NewsModel                                   $newsModel
     * @param \ACP3\Modules\ACP3\News\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers                  $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        News\Model\NewsModel $newsModel,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->formTokenHelper = $formTokenHelper;
        $this->newsModel = $newsModel;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        $defaults = [
            'title' => '',
            'text' => '',
            'uri' => '',
            'link_title' => '',
            'start' => '',
            'end' => '',
        ];

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator('active', 1),
            'categories' => $this->categoriesHelpers->categoriesList(
                News\Installer\Schema::MODULE_NAME,
                null,
                true
            ),
            'options' => $this->fetchOptions(0, 0),
            'target' => $this->formsHelper->linkTargetChoicesGenerator('target'),
            'form' => \array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'SEO_URI_PATTERN' => News\Helpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => '',
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation->validate($formData);

            $formData['cat'] = $this->fetchCategoryIdForSave($formData);
            $formData['user_id'] = $this->user->getUserId();

            return $this->newsModel->save($formData);
        });
    }
}
