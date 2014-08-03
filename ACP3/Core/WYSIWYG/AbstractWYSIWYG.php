<?php
namespace ACP3\Core\WYSIWYG;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Abstract Class for the various WYSIWYG editors
 * @package ACP3\Core\WYSIWYG
 */
abstract class AbstractWYSIWYG extends ContainerAware
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
    protected $config = array();

    abstract public function setParameters(array $params = array());

    abstract public function display();
}