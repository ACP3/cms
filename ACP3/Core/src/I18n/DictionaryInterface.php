<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

interface DictionaryInterface
{
    /**
     * Returns the translation strings for the given language.
     *
     * @return array{info: array<string, string>, keys: array<string, string>}
     */
    public function getDictionary(string $language): array;

    /**
     * Returns the all the registered languages.
     *
     * @return array<string, array{iso: string, name: string}>
     */
    public function getLanguagePacks(): array;
}
