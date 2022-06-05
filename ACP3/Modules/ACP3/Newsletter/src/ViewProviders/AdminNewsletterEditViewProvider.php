<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;

class AdminNewsletterEditViewProvider
{
    public function __construct(private readonly Forms $formsHelper, private readonly FormToken $formTokenHelper, private readonly RequestInterface $request, private readonly SettingsInterface $settings, private readonly Title $title, private readonly Translator $translator)
    {
    }

    /**
     * @param array<string, mixed> $newsletter
     *
     * @return array<string, mixed>
     */
    public function __invoke(array $newsletter): array
    {
        $this->title->setPageTitlePrefix($newsletter['title']);

        $settings = $this->settings->getSettings(NewsletterSchema::MODULE_NAME);

        $actions = [
            1 => $this->translator->t('newsletter', 'send_and_save'),
            0 => $this->translator->t('newsletter', 'only_save'),
        ];

        return [
            'settings' => isset($newsletter['html'])
                ? [...$settings, ...['html' => $newsletter['html']]]
                : $settings,
            'test' => $this->formsHelper->yesNoCheckboxGenerator('test', 0),
            'action' => $this->formsHelper->checkboxGenerator('action', $actions, 1),
            'form' => array_merge($newsletter, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
