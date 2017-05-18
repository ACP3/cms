<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;


interface DictionaryCacheInterface
{
    /**
     * Returns the cached language phrases
     *
     * @param string $locale
     * @return array
     */
    public function getDictionary(string $locale): array;
}
