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
     *
     * @var string
     */
    protected $id;
    /**
     * Name des Formularfeldes, in welchem der WYSIWYG-Editor platziert werden soll.
     *
     * @var string
     */
    protected $name;
    /**
     * Seitenumbrüche aktivieren/deaktivieren.
     *
     * @var bool
     */
    protected $advanced;
    /**
     * @var bool
     */
    protected $required;

    /**
     * Default value of the WYSIWYG editor.
     *
     * @var string
     */
    protected $value;
    /**
     * Config-Array des WYSIWYG-Editors.
     *
     * @var array
     */
    protected $config = [];

    abstract public function setParameters(array $params = []);

    /**
     * Configures the given WYSIWYG-Editor.
     *
     * @return array
     */
    abstract public function getData();

    /**
     * Returns the name of the WYSIWYG Editor.
     *
     * @return string
     */
    abstract public function getFriendlyName();

    /**
     * Returns whether the WYSIWYG-Editor can be used (eg. if it a installed and active).
     *
     * @return bool
     */
    abstract public function isValid();
}
