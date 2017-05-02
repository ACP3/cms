<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;
use Symfony\Component\HttpFoundation\JsonResponse;

class Login extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Users\Model\AuthenticationModel
     */
    protected $authenticationModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;
    /**
     * @var Core\Helpers\Secure
     */
    private $secure;

    /**
     * Login constructor.
     * @param Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Core\Helpers\Secure $secure
     * @param Users\Model\AuthenticationModel $authenticationModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Core\Helpers\Secure $secure,
        Users\Model\AuthenticationModel $authenticationModel
    ) {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
        $this->block = $block;
        $this->secure = $secure;
    }

    /**
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage($this->appPath->getWebRoot());
        }

        return $this->block->render();
    }

    /**
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        try {
            $this->authenticationModel->login(
                $this->secure->strEncode($this->request->getPost()->get('nickname', '')),
                $this->request->getPost()->get('pwd', ''),
                $this->request->getPost()->has('remember')
            );

            if ($this->request->getParameters()->has('redirect')) {
                return $this->redirect()->temporary(
                    base64_decode($this->request->getParameters()->get('redirect'))
                );
            }

            return $this->redirect()->toNewPage($this->appPath->getWebRoot());
        } catch (Users\Exception\LoginFailedException $e) {
            $phrase = $this->translator->t('users', 'nickname_or_password_wrong');
        } catch (Users\Exception\UserAccountLockedException $e) {
            $phrase = $this->translator->t('users', 'account_locked');
        }

        $localizedException = new Core\Authentication\Exception\AuthenticationException($phrase);

        return $this->actionHelper->renderErrorBoxOnFailedFormValidation($localizedException);
    }
}
