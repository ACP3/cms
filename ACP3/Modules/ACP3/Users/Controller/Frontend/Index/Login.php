<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Login
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Index
 */
class Login extends Core\Controller\FrontendAction
{
    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        // Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage($this->appPath->getWebRoot());
        } elseif ($this->request->getPost()->isEmpty() === false) {
            $result = $this->user->login(
                $this->get('core.helpers.secure')->strEncode($this->request->getPost()->get('nickname', '')),
                $this->request->getPost()->get('pwd', ''),
                $this->request->getPost()->has('remember')
            );
            if ($result == 1) {
                if ($this->request->getParameters()->has('redirect')) {
                    return $this->redirect()->temporary(base64_decode($this->request->getParameters()->get('redirect')));
                }

                return $this->redirect()->toNewPage($this->appPath->getWebRoot());
            }

            return [
                'error_msg' => $this->get('core.helpers.alerts')->errorBox($this->translator->t('users',
                    $result == -1 ? 'account_locked' : 'nickname_or_password_wrong'))
            ];
        }
    }
}
