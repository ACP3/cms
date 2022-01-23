<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\WYSIWYG\Editor;

/**
 * Abstract Class for the various WYSIWYG editors.
 */
abstract class AbstractWYSIWYG
{
    /**
     * ID des WYSIWYG-Editors.
     */
    protected string $id;
    /**
     * Name des Formularfeldes, in welchem der WYSIWYG-Editor platziert werden soll.
     */
    protected string $name;
    /**
     * Seitenumbrüche aktivieren/deaktivieren.
     */
    protected bool $advanced;

    protected bool $required;

    /**
     * Default value of the WYSIWYG editor.
     */
    protected string $value;
    /**
     * Config-Array des WYSIWYG-Editors.
     *
     * @var array<string, mixed>
     */
    protected array $config = [];

    /**
     * @param array<string, mixed> $params
     */
    abstract public function setParameters(array $params = []): void;

    /**
     * Configures the given WYSIWYG-Editor.
     *
     * @return array<string, mixed>
     */
    abstract public function getData(): array;

    /**
     * Returns the name of the WYSIWYG Editor.
     */
    abstract public function getFriendlyName(): string;

    /**
     * Returns whether the WYSIWYG-Editor can be used (eg. if it a installed and active).
     */
    abstract public function isValid(): bool;
}
