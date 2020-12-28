<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Users;

class Register extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\RegistrationViewProvider
     */
    private $registrationViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        UserModelInterface $user,
        Core\Http\RedirectResponse $redirectResponse,
        Core\Helpers\Alerts $alertsHelper,
        Users\ViewProviders\RegistrationViewProvider $registrationViewProvider
    ) {
        parent::__construct($context);

        $this->alertsHelper = $alertsHelper;
        $this->redirectResponse = $redirectResponse;
        $this->registrationViewProvider = $registrationViewProvider;
        $this->user = $user;
    }

    public function __invoke()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        $settings = $this->config->getSettings(Users\Installer\Schema::MODULE_NAME);

        if ($settings['enable_registration'] == 0) {
            return $this->alertsHelper->errorBox(
                $this->translator->t('users', 'user_registration_disabled')
            );
        }

        return ($this->registrationViewProvider)();
    }
}
