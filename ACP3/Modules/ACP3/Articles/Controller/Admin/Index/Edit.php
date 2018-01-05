<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Articles;

class Edit extends AbstractFrontendAction
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

        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->articlesModel = $articlesModel;
        $this->formsHelper = $formsHelper;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $article = $this->articlesModel->getOneById($id);

        if (empty($article) === false) {
            $this->title->setPageTitlePrefix($article['title']);

            return [
                'active' => $this->formsHelper->yesNoCheckboxGenerator('active', $article['active']),
                'form' => array_merge($article, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
                'SEO_URI_PATTERN' => Articles\Helpers::URL_KEY_PATTERN,
                'SEO_ROUTE_NAME' => sprintf(Articles\Helpers::URL_KEY_PATTERN, $id)
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation
                ->setUriAlias(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->articlesModel->save($formData, $id);
        });
    }
}
