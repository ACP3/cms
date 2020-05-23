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
     * @var \ACP3\Modules\ACP3\News\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var News\Model\NewsModel
     */
    private $newsModel;
    /**
     * @var \ACP3\Modules\ACP3\News\ViewProviders\AdminNewsEditViewProvider
     */
    private $adminNewsEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        News\Model\NewsModel $newsModel,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers,
        News\ViewProviders\AdminNewsEditViewProvider $adminNewsEditViewProvider
    ) {
        parent::__construct($context, $categoriesHelpers);

        $this->newsModel = $newsModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->adminNewsEditViewProvider = $adminNewsEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $defaults = [
            'active' => 1,
            'category_id' => null,
            'readmore' => 0,
            'id' => null,
            'title' => '',
            'target' => null,
            'text' => '',
            'uri' => '',
            'link_title' => '',
            'start' => '',
            'end' => '',
        ];

        return ($this->adminNewsEditViewProvider)($defaults);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
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
