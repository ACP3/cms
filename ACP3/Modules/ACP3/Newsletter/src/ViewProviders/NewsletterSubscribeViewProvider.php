<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class NewsletterSubscribeViewProvider
{
    public function __construct(private readonly Forms $formsHelper, private readonly FormToken $formTokenHelper, private readonly RequestInterface $request, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $defaults = [
            'first_name' => '',
            'last_name' => '',
            'mail' => '',
        ];

        $salutations = [
            0 => $this->translator->t('newsletter', 'salutation_unspecified'),
            1 => $this->translator->t('newsletter', 'salutation_female'),
            2 => $this->translator->t('newsletter', 'salutation_male'),
        ];

        return [
            'salutation' => $this->formsHelper->choicesGenerator('salutation', $salutations),
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
