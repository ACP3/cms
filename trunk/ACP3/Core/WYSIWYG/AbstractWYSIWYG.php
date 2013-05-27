<?php
namespace ACP3\Core\WYSIWYG;

/**
 * Abstract Class for the various WYSIWYG editors
 *
 * @author Tino Goratsch
 */
abstract class AbstractWYSIWYG {
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
	 * Seitenumbrücke aktivieren/deaktivieren
	 * @var boolean
	 */
	protected $advanced;
	/**
	 * Config-Array des WYSIWYG-Editors
	 * 
	 * @var array
	 */
	protected $config = array();

	abstract public function __construct($id, $name, $value = '', $toolbar = '', $advanced = false, $height = '');

	public function setConfig($key, $value) {
		$this->config[$key] = $value;
	}

	abstract protected function configure();

	abstract public function display();
}