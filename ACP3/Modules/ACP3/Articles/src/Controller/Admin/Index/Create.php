<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

class Create extends Core\Controller\AbstractFrontendAction
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

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Articles\ViewProviders\AdminArticleEditViewProvider $adminArticleEditViewProvider,
        Articles\Model\ArticlesModel $articlesModel,
        Articles\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->adminArticleEditViewProvider = $adminArticleEditViewProvider;
    }

    /**
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function execute(): array
    {
        $defaults = [
            'active' => 1,
            'layout' => '',
            'start' => '',
            'end' => '',
            'title' => '',
            'subtitle' => '',
            'text' => '',
        ];

        return ($this->adminArticleEditViewProvider)($defaults);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
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
