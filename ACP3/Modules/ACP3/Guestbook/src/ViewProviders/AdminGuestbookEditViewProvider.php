<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema as GuestbookSchema;

class AdminGuestbookEditViewProvider
{
    public function __construct(private Forms $formsHelper, private FormToken $formTokenHelper, private RequestInterface $request, private SettingsInterface $settings, private Title $title)
    {
    }

    public function __invoke(array $guestbookEntry): array
    {
        $settings = $this->settings->getSettings(GuestbookSchema::MODULE_NAME);

        $this->title->setPageTitlePrefix($guestbookEntry['name']);

        return [
            'form' => array_merge($guestbookEntry, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'activate' => $settings['notify'] == 2
                ? $this->formsHelper->yesNoCheckboxGenerator('active', $guestbookEntry['active'])
                : [],
        ];
    }
}
