<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class CreatePost extends AbstractFormAction
{
    public function __construct(
        Core\Controller\Context\Context $context,
        private FormAction $actionHelper,
        private UserModelInterface $user,
        private News\Model\NewsModel $newsModel,
        private News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $categoriesHelpers);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|Response
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
