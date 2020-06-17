<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

class Edit extends AbstractFormAction
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
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        UserModelInterface $user,
        News\Model\NewsModel $newsModel,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers,
        News\ViewProviders\AdminNewsEditViewProvider $adminNewsEditViewProvider
    ) {
        parent::__construct($context, $categoriesHelpers);

        $this->newsModel = $newsModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->adminNewsEditViewProvider = $adminNewsEditViewProvider;
        $this->user = $user;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        $news = $this->newsModel->getOneById($id);

        if (empty($news) === false) {
            return ($this->adminNewsEditViewProvider)($news);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation
                ->setUriAlias(\sprintf(News\Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $formData['cat'] = $this->fetchCategoryIdForSave($formData);
            $formData['user_id'] = $this->user->getUserId();

            return $this->newsModel->save($formData, $id);
        });
    }
}
