<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;

interface LocaleInterface
{
    /**
     * Gets the full locale name (e.g. en_US)
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Gets the short ISO language code (e.g en)
     *
     * @return string
     */
    public function getShortIsoCode(): string;

    /**
     * Gets the writing direction of the language
     *
     * @return string
     */
    public function getDirection(): string;
}
