<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

interface SchemaInterface
{
    /**
     * Liefert ein Array mit den zu erstellenden Datenbanktabellen des Moduls zurück.
     *
     * @return string[]
     */
    public function createTables(): array;

    /**
     * Liefert ein Array mit den zu löschenden Datenbanktabellen des Moduls zurück.
     *
     * @return string[]
     */
    public function removeTables(): array;

    /**
     * Liefert ein Array mit den zu erstellenden Moduleinstellungen zurück.
     *
     * @return array<string, string|int|bool|float>
     */
    public function settings(): array;

    /**
     * Returns a nested map of resources which need some kind of protection.
     *
     * @return array<string, array<string, array<string, PrivilegeEnum>>>
     */
    public function specialResources(): array;

    /**
     * Returns the name of the module.
     */
    public function getModuleName(): string;
}
