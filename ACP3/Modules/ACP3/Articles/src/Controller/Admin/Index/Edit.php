<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
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
        Articles\Model\ArticlesModel $articlesModel
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
        $this->adminArticleEditViewProvider = $adminArticleEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function __invoke(int $id): array
    {
        $article = $this->articlesModel->getOneById($id);

        if (empty($article) === false) {
            return ($this->adminArticleEditViewProvider)($article);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
