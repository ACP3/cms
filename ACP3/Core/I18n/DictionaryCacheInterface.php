<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

/**
 * Created by PhpStorm.
 * User: tinog
 * Date: 18.05.2017
 * Time: 17:25
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
    public function getLanguageCache(string $locale): array;
}
