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
     * @var \ACP3\Modules\ACP3\Comments\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var Comments\Model\CommentsModel
     */
    protected $commentsModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext         $context
     * @param Core\View\Block\FormBlockInterface                    $block
     * @param Comments\Model\CommentsModel                          $commentsModel
     * @param \ACP3\Modules\ACP3\Comments\Validation\FormValidation $formValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Validation\FormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->commentsModel = $commentsModel;
        $this->block = $block;
    }

    /**
     * @param string $module
     * @param int    $entryId
     * @param string $redirectUrl
     *
     * @return array
     */
    public function execute(string $module, int $entryId, string $redirectUrl)
    {
        return $this->block
            ->setData([
                'module' => $module,
                'entryId' => $entryId,
                'redirectUrl' => $redirectUrl,
            ])
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param string $module
     * @param int    $entryId
     * @param string $redirectUrl
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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

                $result = $this->commentsModel->save($formData);

                return $this->redirectMessages()->setMessage(
                    $result,
                    $this->translator->t('system', $result !== false ? 'create_success' : 'create_error'),
                    \base64_decode(\urldecode($redirectUrl))
                );
            }
        );
    }
}
