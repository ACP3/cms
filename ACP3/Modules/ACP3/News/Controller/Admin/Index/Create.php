<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\News\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var News\Model\NewsModel
     */
    protected $newsModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param News\Model\NewsModel $newsModel
     * @param \ACP3\Modules\ACP3\News\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        News\Model\NewsModel $newsModel,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $categoriesHelpers);

        $this->newsModel = $newsModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
