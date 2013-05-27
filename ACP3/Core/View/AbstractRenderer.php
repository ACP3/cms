<?php
namespace ACP3\Core\View;

/**
 * Abstract Class for the various renderers
 */
abstract class AbstractRenderer {
	protected $config = array();
	public $renderer = null;

	public function __construct(array $params = array()) {
		$this->config = $params;
	}

	abstract public function assign($name, $value = null);

	abstract public function fetch($template);

	abstract public function display($template);
	
	abstract public function templateExists($template);
}