<?php
namespace ACP3\Core\View;

class Smarty extends AbstractRenderer {
	public function __construct()
	{
		require LIBRARIES_DIR . 'smarty/Smarty.class.php';
		$this->renderer = new \Smarty();
		$this->renderer->error_reporting = defined('DEBUG') === true && DEBUG === true ? E_ALL : 0;
		$this->renderer->compile_id = CONFIG_DESIGN;
		$this->renderer->setCompileCheck(defined('DEBUG') === true && DEBUG === true);
		$this->renderer->setTemplateDir(array(DESIGN_PATH_INTERNAL, MODULES_DIR))
			->addPluginsDir(CLASSES_DIR . 'View/SmartyHelpers/')
			->setCompileDir(CACHE_DIR . 'tpl_compiled/')
			->setCacheDir(CACHE_DIR . 'tpl_cached/');
		$this->renderer->registerClass('Validate', "\ACP3\Core\Validate");
	}

	public function assign($name, $value = null)
	{
		if (is_array($name)) {
			$this->renderer->assign($name);
		} else {
			$this->renderer->assign($name, $value);
		}
	}

	public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
	{
		return $this->renderer->fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false);
	}

	public function display($template, $cache_id = null, $compile_id = null, $parent = null)
	{
		echo $this->renderer->display($template, $cache_id = null, $compile_id = null, $parent = null);
	}
}