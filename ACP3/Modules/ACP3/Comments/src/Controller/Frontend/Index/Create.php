<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
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

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Modules $modules,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Validation\FormValidation $formValidation,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->commentsModel = $commentsModel;
        $this->modules = $modules;
    }

    public function execute(string $module, int $entryId, string $redirectUrl): array
    {
        return [
            'form' => \array_merge($this->fetchFormDefaults(), $this->request->getPost()->all()),
            'module' => $module,
            'entry_id' => $entryId,
            'redirect_url' => $redirectUrl,
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
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

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'create_success' : 'create_error'),
                    \base64_decode(\urldecode($redirectUrl))
                );
            }
        );
    }

    private function fetchFormDefaults(): array
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $user = $this->user->getUserInfo();
            $defaults['name'] = $user['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['message'] = '';
        }

        return $defaults;
    }
}
