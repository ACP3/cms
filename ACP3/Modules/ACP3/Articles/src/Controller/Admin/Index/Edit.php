<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Articles;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Articles\Model\ArticlesModel
     */
    private $articlesModel;
    /**
     * @var \ACP3\Modules\ACP3\Articles\ViewProviders\AdminArticleEditViewProvider
     */
    private $adminArticleEditViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        UserModelInterface $user,
        Articles\ViewProviders\AdminArticleEditViewProvider $adminArticleEditViewProvider,
        Articles\Model\ArticlesModel $articlesModel,
        Articles\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->articlesModel = $articlesModel;
        $this->adminArticleEditViewProvider = $adminArticleEditViewProvider;
        $this->user = $user;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function execute(int $id): array
    {
        $article = $this->articlesModel->getOneById($id);

        if (empty($article) === false) {
            return ($this->adminArticleEditViewProvider)($article);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation
                ->setUriAlias(\sprintf(Articles\Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->articlesModel->save($formData, $id);
        });
    }
}
