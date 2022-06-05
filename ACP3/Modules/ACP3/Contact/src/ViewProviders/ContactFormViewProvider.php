<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\ViewProviders;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Contact\Installer\Schema as ContactSchema;

class ContactFormViewProvider
{
    public function __construct(private readonly FormToken $formTokenHelper, private readonly RequestInterface $request, private readonly SettingsInterface $settings, private readonly UserModelInterface $user)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'form' => array_merge($this->getFormDefaults(), $this->request->getPost()->all()),
            'contact' => $this->settings->getSettings(ContactSchema::MODULE_NAME),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getFormDefaults(): array
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'mail' => '',
            'mail_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $user = $this->user->getUserInfo();
            $defaults['name'] = !empty($user['realname']) ? $user['realname'] : $user['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['mail'] = $user['mail'];
            $defaults['mail_disabled'] = true;
        }

        return $defaults;
    }
}
