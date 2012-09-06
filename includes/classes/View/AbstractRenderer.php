<?php

interface ACP3_View_RendererInterface {
	
}
abstract class ACP3_View_AbstractRenderer implements ACP3_View_RendererInterface {
	protected $config = array();
	public $renderer = null;

	public function __construct(array $params = array()) {
		$this->config = $params;
	}

	abstract public function assign($name, $value = null);

	abstract public function fetch($template);

	abstract public function display($template);
}