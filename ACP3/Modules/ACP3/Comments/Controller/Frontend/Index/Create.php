<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Comments\Controller\Frontend\Index
 */
class Create extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var Comments\Model\CommentsModel
     */
    protected $commentsModel;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Comments\Model\CommentsModel $commentsModel
     * @param \ACP3\Modules\ACP3\Comments\Validation\FormValidation $formValidation
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Validation\FormValidation $formValidation,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->commentsModel = $commentsModel;
    }

    /**
     * @param string $module
     * @param int $entryId
     * @param string $redirectUrl
     * @return array
     */
    public function execute($module, $entryId, $redirectUrl)
    {
        return [
            'form' => array_merge($this->fetchFormDefaults(), $this->request->getPost()->all()),
            'module' => $module,
            'entry_id' => $entryId,
            'redirect_url' => $redirectUrl,
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'can_use_emoticons' => $this->emoticonsActive === true
        ];
    }

    /**
     * @param string $module
     * @param int $entryId
     * @param string $redirectUrl
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($module, $entryId, $redirectUrl)
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
                    base64_decode(urldecode($redirectUrl))
                );
            }
        );
    }

    /**
     * @return array
     */
    private function fetchFormDefaults()
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'message' => ''
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
