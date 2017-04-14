<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
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
     * @var Core\View\Block\FormTemplateInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormTemplateInterface $block
     * @param Articles\Model\ArticlesModel $articlesModel
     * @param \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormTemplateInterface $block,
        Articles\Model\ArticlesModel $articlesModel,
        Articles\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->articlesModel = $articlesModel;
        $this->block = $block;
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
            return $this->block
                ->setData($article)
                ->setRequestData($this->request->getPost()->all())
                ->render();
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
            $result = $this->articlesModel->save($formData, $id);

            $this->createOrUpdateMenuItem($formData, $id);

            return $result;
        });
    }
}
