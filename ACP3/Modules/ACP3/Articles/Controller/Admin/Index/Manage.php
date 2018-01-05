<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Articles;

class Manage extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Articles\Model\ArticlesModel
     */
    protected $articlesModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * Create constructor.
     * @param Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Articles\Model\ArticlesModel $articlesModel
     * @param Articles\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Articles\Model\ArticlesModel $articlesModel,
        Articles\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
        $this->adminFormValidation = $adminFormValidation;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
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
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            if ($id !== null) {
                $this->adminFormValidation->setUriAlias(\sprintf(Articles\Helpers::URL_KEY_PATTERN, $id));
            }

            $this->adminFormValidation->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->articlesModel->save($formData, $id);
        });
    }
}
