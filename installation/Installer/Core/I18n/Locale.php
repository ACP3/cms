<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\I18n;


use ACP3\Core\I18n\LocaleInterface;

class Locale implements LocaleInterface
{
    /**
     * @var string
     */
    private $locale = '';

    /**
     * Locale constructor.
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Gets the full locale name (e.g. en_US)
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Gets the short ISO language code (e.g en)
     *
     * @return string
     */
    public function getShortIsoCode(): string
    {
        return substr($this->getLocale(), 0, strpos($this->getLocale(), '_'));
    }

    /**
     * Gets the writing direction of the language
     *
     * @return string
     */
    public function getDirection(): string
    {
        // TODO: Implement getDirection() method.
    }
}
