<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\I18n;

use ACP3\Core\I18n\Translator;

class TranslatorConfigurator
{
    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function configure(Translator $translator): void
    {
        $translator->setLocale($this->defaultLocale);
    }
}
