<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Manage extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation
     */
    protected $pollsValidator;
    /**
     * @var Polls\Model\PollsModel
     */
    protected $pollsModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Polls\Model\PollsModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation $pollsValidator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Polls\Model\PollsModel $pollsModel,
        Polls\Validation\AdminFormValidation $pollsValidator
    ) {
        parent::__construct($context);

        $this->pollsModel = $pollsModel;
        $this->pollsValidator = $pollsValidator;
        $this->block = $block;
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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->pollsValidator->validate($formData);

            $formData['user_id'] = $this->user->getUserId();
            $result = $this->pollsModel->save($formData, $id);

            if (!empty($formData['reset'])) {
                $this->pollsModel->resetVotesByPollId($id);
            }

            $resultAnswers = false;
            if ($result !== false) {
                $resultAnswers = $this->pollsModel->saveAnswers($formData['answers'], $result);
            }

            return $result !== false && $resultAnswers !== false;
        });
    }
}
