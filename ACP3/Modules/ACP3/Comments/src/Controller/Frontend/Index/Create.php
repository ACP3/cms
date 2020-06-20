<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Comments;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\FormValidation
     */
    private $formValidation;
    /**
     * @var Comments\Model\CommentsModel
     */
    private $commentsModel;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Comments\ViewProviders\CommentCreateViewProvider
     */
    private $commentCreateViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Core\Modules $modules,
        UserModelInterface $user,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Validation\FormValidation $formValidation,
        Comments\ViewProviders\CommentCreateViewProvider $commentCreateViewProvider
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->commentsModel = $commentsModel;
        $this->modules = $modules;
        $this->commentCreateViewProvider = $commentCreateViewProvider;
        $this->user = $user;
        $this->actionHelper = $actionHelper;
    }

    public function execute(string $module, int $entryId, string $redirectUrl): array
    {
        return ($this->commentCreateViewProvider)($module, $entryId, $redirectUrl);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(string $module, int $entryId, string $redirectUrl)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($module, $entryId, $redirectUrl) {
                $formData = $this->request->getPost()->all();
                $ipAddress = $this->request->getSymfonyRequest()->getClientIp();

                $this->formValidation
                    ->setIpAddress($ipAddress)
                    ->validate($formData);

                $formData['date'] = 'now';
                $formData['ip'] = $ipAddress;
                $formData['user_id'] = $this->user->isAuthenticated() === true ? $this->user->getUserId() : null;
                $formData['module_id'] = $this->modules->getModuleId($module);
                $formData['entry_id'] = $entryId;

                $bool = $this->commentsModel->save($formData);

                return $this->actionHelper->setRedirectMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'create_success' : 'create_error'),
                    \base64_decode(\urldecode($redirectUrl))
                );
            }
        );
    }
}
