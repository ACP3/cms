<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Create extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var Articles\Model\ArticlesModel
     */
    protected $articlesModel;
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param Articles\Model\ArticlesModel $articlesModel
     * @param \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Articles\Model\ArticlesModel $articlesModel,
        Articles\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->formsHelper = $formsHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $defaults = [
            'title' => '',
            'text' => '',
            'start' => '',
            'end' => ''
        ];

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator('active', 1),
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'SEO_URI_PATTERN' => Articles\Helpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => ''
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();
            $this->adminFormValidation->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->articlesModel->save($formData);
        });
    }
}
