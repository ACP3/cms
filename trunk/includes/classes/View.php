<?php
/**
 * View
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

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
	private $js_libraries = array('bootbox' => false, 'fancybox' => false, 'jquery-ui' => false, 'timepicker' => false, 'datatables' => false);
	
	private static $renderer_obj = null;

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

	public static function getRenderer()
	{
		return self::$renderer_obj;
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

	public static function factory($renderer = 'Smarty', $params = '')
	{
		$path = INCLUDES_DIR . 'classes/View/' . $renderer . '.php';
		if (is_file($path) === true) {
			$class_name = 'ACP3_View_' . $renderer;
			if (class_exists($class_name) === false) {
				require $path;
			}

			if (class_exists($class_name) === true) {
				self::$renderer_obj = new $class_name($params);
			} else {
				throw new Exception('Class ' . $class_name . ' no found!');
			}
		} else {
			throw new Exception('File ' . $path . ' not found!');
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
		if ($this->templateExists($template)) {
			return self::$renderer_obj->fetch($template, $cache_id, $compile_id, $parent, $display);
		} else {
			// Pfad zerlegen
			$path = explode('/', $template);
			if (count($path) > 1 && $this->templateExists($path[0] . '/templates/' . $path[1])) {
				return self::$renderer_obj->fetch($path[0] . '/templates/' . $path[1], $cache_id, $compile_id, $parent, $display);
			} else {
				return sprintf(ACP3_CMS::$lang->t('errors', 'tpl_not_found'), $template);
			}
		}
	}

	public function templateExists($template)
	{
		if (is_file(DESIGN_PATH_INTERNAL . $template) === true ||
			is_file(MODULES_DIR . $template) === true)
			return true;
		return false;
	}

	/**
	 * Weist dem View-Object eine Template-Variable zu
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return boolean
	 */
	public function assign($name, $value = null)
	{
		return self::$renderer_obj->assign($name, $value);
	}
}