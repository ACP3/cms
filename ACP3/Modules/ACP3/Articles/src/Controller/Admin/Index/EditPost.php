<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Articles;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private FormAction $actionHelper,
        private UserModelInterface $user,
        private Articles\Model\ArticlesModel $articlesModel,
        private Articles\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(int $id): array|string|Response
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
