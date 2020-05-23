<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation
     */
    private $pollsValidator;
    /**
     * @var Polls\Model\PollsModel
     */
    private $pollsModel;
    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\AdminPollEditViewProvider
     */
    private $adminPollEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Polls\Model\PollsModel $pollsModel,
        Polls\Validation\AdminFormValidation $pollsValidator,
        Polls\ViewProviders\AdminPollEditViewProvider $adminPollEditViewProvider
    ) {
        parent::__construct($context);

        $this->pollsModel = $pollsModel;
        $this->pollsValidator = $pollsValidator;
        $this->adminPollEditViewProvider = $adminPollEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $defaults = [
            'id' => null,
            'multiple' => 0,
            'title' => '',
            'start' => '',
            'end' => '',
        ];

        return ($this->adminPollEditViewProvider)($defaults);
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

            $this->pollsValidator->validate($formData);

            $formData['user_id'] = $this->user->getUserId();
            $pollId = $this->pollsModel->save($formData);

            $bool2 = false;
            if ($pollId !== false) {
                $bool2 = $this->pollsModel->saveAnswers($formData['answers'], $pollId);
            }

            return $pollId !== false && $bool2 !== false;
        });
    }
}
