<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

class Manage extends Core\Controller\AbstractFrontendAction
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
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;
    /**
     * @var Categories\Helpers
     */
    private $categoriesHelpers;

    /**
     * Manage constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param News\Model\NewsModel $newsModel
     * @param \ACP3\Modules\ACP3\News\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        News\Model\NewsModel $newsModel,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context);

        $this->newsModel = $newsModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->block = $block;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param int|null $id
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function execute(?int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int|null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            if ($id !== null) {
                $this->adminFormValidation->setUriAlias(\sprintf(News\Helpers::URL_KEY_PATTERN, $id));
            }

            $this->adminFormValidation->validate($formData);

            $formData['cat'] = $this->fetchCategoryIdForSave($formData);
            $formData['user_id'] = $this->user->getUserId();

            return $this->newsModel->save($formData, $id);
        });
    }

    /**
     * @param array $formData
     *
     * @return int
     */
    protected function fetchCategoryIdForSave(array $formData): int
    {
        return !empty($formData['cat_create'])
            ? $this->categoriesHelpers->categoryCreate($formData['cat_create'], News\Installer\Schema::MODULE_NAME)
            : $formData['cat'];
    }
}
