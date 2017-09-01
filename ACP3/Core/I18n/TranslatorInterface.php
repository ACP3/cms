<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;

interface TranslatorInterface
{
    /**
     * Translates a given phrase of a given module into some language
     *
     * @param string $module
     * @param string $phrase
     * @param array $arguments
     *
     * @return string
     */
    public function t(string $module, string $phrase, array $arguments = []): string;
}
