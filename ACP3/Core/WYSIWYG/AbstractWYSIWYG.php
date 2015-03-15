<?php
namespace ACP3\Core\WYSIWYG;

/**
 * Abstract Class for the various WYSIWYG editors
 * @package ACP3\Core\WYSIWYG
 */
abstract class AbstractWYSIWYG
{
    /**
     * ID des WYSIWYG-Editors
     * @var string
     */
    protected $id;
    /**
     * Name des Formularfeldes, in welchem der WYSIWYG-Editor platziert werden soll
     * @var string
     */
    protected $name;
    /**
     * Seitenumbrüche aktivieren/deaktivieren
     * @var boolean
     */
    protected $advanced;
    /**
     * Default value of the WYSIWYG editor
     * @var string
     */
    protected $value;
    /**
     * Config-Array des WYSIWYG-Editors
     * @var array
     */
    protected $config = [];

    /**
     * @param array $params
     */
    abstract public function setParameters(array $params = []);

    /**
     * Configures the given WYSIWYG-Editor
     *
     * @return string
     */
    abstract public function display();

    /**
     * Returns the name of the WYSIWYG Editor
     *
     * @return string
     */
    abstract public function getFriendlyName();
}
