<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Environment;

interface ThemePathInterface
{
    /**
     * Return all the currently registered themes.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableThemes(): array;

    /**
     * Returns, whether the given theme name exists within the system or not.
     */
    public function has(string $themeName): bool;

    /**
     * Returns the name of the current theme.
     */
    public function getCurrentTheme(): string;

    /**
     * Returns the internal directory path of the given theme.
     * If no theme name is supplied, it returns the directory path of the current theme.
     */
    public function getDesignPathInternal(?string $themeName = null): string;

    /**
     * Returns the "pretty" directory path of the given theme.
     * If no theme name is supplied, it returns the directory path of the current theme.
     */
    public function getDesignPathWeb(?string $themeName = null): string;

    /**
     * @return string[]
     */
    public function getThemeDependencies(string $themeName): array;

    /**
     * @return string[]
     */
    public function getCurrentThemeDependencies(): array;

    /**
     * @return array<string, mixed>
     */
    public function getCurrentThemeInfo(): array;
}
