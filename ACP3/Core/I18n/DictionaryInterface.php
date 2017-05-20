<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;


interface DictionaryInterface
{
    /**
     * Returns the cached language phrases
     *
     * @param string $locale
     * @return array
     */
    public function getDictionary(string $locale): array;

    /**
     * Saves the found language phrases of the given locale to some cache
     *
     * @param string $locale
     *
     * @return bool
     */
    public function saveDictionary(string $locale): bool;
}
