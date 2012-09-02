<?php
/**
 * View
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Klasse für die Ausgabe der Seite
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_View
{
	/**
	 * Nicht ausgeben
	 */
	private $no_output = false;

	/**
	 * Der auszugebende Content-Type der Seite
	 *
	 * @var string
	 */
	private $content_type = 'Content-Type: text/html; charset=UTF-8';

	/**
	 * Das zuverwendende Seitenlayout
	 *
	 * @var string
	 */
	private $layout = 'layout.tpl';

	/**
	 * Legt fest, welche JavaScript Bibliotheken beim Seitenaufruf geladen werden sollen
	 * 
	 * @var array
	 */
	private $js_libraries = array('bootbox' => false, 'fancybox' => false, 'jquery-ui' => false, 'timepicker' => false);
	
	private $view_obj = null;

	/**
	 * Setter Methode für die $this->no_output Variable
	 *
	 * @param boolean $value
	 */
	public function setNoOutput($value)
	{
		$this->no_output = (bool) $value;
	}

	/**
	 * Gibt zurück, ob die Seitenausgabe mit Hilfe der ACP3_Bootstrap-Klasse
	 * erfolgen soll oder die Datei dies selber handelt
	 *
	 * @return string
	 */
	public function getNoOutput()
	{
		return $this->no_output;
	}

	/**
	 * Weist der aktuell auszugebenden Seite den Content-Type zu
	 *
	 * @param string $data
	 */
	public function setContentType($data)
	{
		$this->content_type = $data;
	}

	/**
	 * Gibt den Content-Type der anzuzeigenden Seiten zurück
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return $this->content_type;
	}

	/**
	 * Weist der aktuell auszugebenden Seite ein Layout zu
	 *
	 * @param string $file
	 */
	public function setLayout($file)
	{
		$this->layout = $file;
	}

	/**
	 * Gibt das aktuell zugewiesene Layout zurück
	 *
	 * @return string
	 */
	public function getLayout()
	{
		return $this->layout;
	}
	/**
	 * Aktiviert einzelne JavaScript Bibliotheken
	 *
	 * @param array $libraries
	 * @return
	 */
	public function enableJsLibraries(array $libraries)
	{
		foreach ($libraries as $library) {
			if (array_key_exists($library, $this->js_libraries) === true) {
				$this->js_libraries[$library] = true;
				if ($library === 'timepicker')
					$this->js_libraries['jquery-ui'] = true;
			}
		}
		return;
	}

	public function __construct()
	{
		// Smarty einbinden
		require LIBRARIES_DIR . 'smarty/Smarty.class.php';
		$this->view_obj = new Smarty();
		$this->view_obj->error_reporting = defined('DEBUG') === true && DEBUG === true ? E_ALL : 0;
		$this->view_obj->compile_id = CONFIG_DESIGN;
		$this->view_obj->setCompileCheck(defined('DEBUG') === true && DEBUG === true);
		$this->view_obj->setTemplateDir(array(DESIGN_PATH_INTERNAL, MODULES_DIR))
			->addPluginsDir(INCLUDES_DIR . 'smarty_functions/')
			->setCompileDir(ACP3_ROOT . 'uploads/cache/tpl_compiled/')
			->setCacheDir(ACP3_ROOT . 'uploads/cache/tpl_cached/');
		if (is_writable($this->view_obj->getCompileDir()) === false || is_writable($this->view_obj->getCacheDir()) === false) {
			exit('The cache folder is not writable!');
		}
	}

	/**
	 * Erstellt den Link zum Minifier mitsamt allen zu ladenden JavaScript Bibliotheken
	 *
	 * @return string
	 */
	public function buildMinifyLink()
	{
		$minify = ROOT_DIR . 'includes/min/' . ((bool) CONFIG_SEO_MOD_REWRITE === true && defined('IN_ADM') === false ? '' : '?') . 'g=%s';

		ksort($this->js_libraries);
		$libraries = '';
		foreach ($this->js_libraries as $library => $enable) {
			if ($enable === true)
				$libraries.= $library . ',';
		}

		if ($libraries !== '')
			$minify.= '&amp;libraries=' . substr($libraries, 0, -1) . '&amp;' . CONFIG_DESIGN;
		
		return $minify;
	}
	/**
	 * Gibt ein Template direkt aus
	 *
	 * @param string $template
	 * @param mixed $cache_id
	 * @param integer $cache_lifetime
	 */
	public function displayTemplate($template, $cache_id = null, $compile_id = null, $parent = null)
	{
		echo $this->fetchTemplate($template, $cache_id, $compile_id, $parent, true);
	}
	/**
	 * Gibt ein Template aus
	 *
	 * @param string $template
	 * @param mixed $cache_id
	 * @param mixed $compile_id
	 * @param object $parent
	 * @param boolean $display
	 * @return string
	 */
	public function fetchTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
	{
		if ($this->view_obj->templateExists($template)) {
			return $this->view_obj->fetch($template, $cache_id, $compile_id, $parent, $display);
		} else {
			// Pfad zerlegen
			$path = explode('/', $template);
			if (count($path) > 1 && $this->view_obj->templateExists($path[0] . '/templates/' . $path[1])) {
				return $this->view_obj->fetch($path[0] . '/templates/' . $path[1], $cache_id, $compile_id, $parent, $display);
			} else {
				return sprintf(ACP3_CMS::$lang->t('errors', 'tpl_not_found'), $template);
			}
		}
	}

	/**
	 * Weist dem View-Object eine Template-Variable zu
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return boolean
	 */
	public function assign($name, $value)
	{
		return $this->view_obj->assign($name, $value);
	}
}