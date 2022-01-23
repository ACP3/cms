<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\ViewProviders;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema as GuestbookSchema;

class AdminSettingsViewProvider
{
    public function __construct(private Date $dateHelper, private Forms $formsHelper, private FormToken $formTokenHelper, private RequestInterface $request, private SettingsInterface $settings, private Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(GuestbookSchema::MODULE_NAME);

        $notificationTypes = [
            0 => $this->translator->t('guestbook', 'no_notification'),
            1 => $this->translator->t('guestbook', 'notify_on_new_entry'),
            2 => $this->translator->t('guestbook', 'notify_and_enable'),
        ];

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'notify' => $this->formsHelper->choicesGenerator('notify', $notificationTypes, $settings['notify']),
            'overlay' => $this->formsHelper->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'form' => array_merge(['notify_email' => $settings['notify_email']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
