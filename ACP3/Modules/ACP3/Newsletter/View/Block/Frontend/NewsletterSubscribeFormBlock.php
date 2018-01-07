<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractFormBlock;

class NewsletterSubscribeFormBlock extends AbstractFormBlock
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $salutations = [
            0 => $this->translator->t('newsletter', 'salutation_unspecified'),
            1 => $this->translator->t('newsletter', 'salutation_female'),
            2 => $this->translator->t('newsletter', 'salutation_male'),
        ];

        return [
            'salutation' => $this->forms->choicesGenerator('salutation', $salutations),
            'form' => \array_merge($this->getData(), $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'first_name' => '',
            'last_name' => '',
            'mail' => '',
        ];
    }
}
