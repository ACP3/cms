<?php
/**
 * Modules
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Klasse für die Module
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class modules
{
	/**
	 * Überpüft, ob ein Modul überhaupt existiert,
	 * bzw. der Benutzer auf ein Modul Zugriff hat
	 *
	 * @param string $module
	 * 	Zu überprüfendes Modul
	 * @param string $file
	 * 	Zu überprüfende Moduldatei
	 *
	 * @return integer
	 */
	public static function check($module = 0, $file = 0) {
		global $uri;

		$module = !empty($module) ? $module : $uri->mod;
		$file = !empty($file) ? $file : $uri->file;

		if (is_file(MODULES_DIR . '' . $module . '/' . $file . '.php') === true) {
			if (self::isActive($module) === true) {
				return acl::canAccessResource($module . '/' . $file . '/');
			}
			return 0;
		}
		return -1;
	}
	/**
	 * Gibt zurück, ob ein Modul aktiv ist oder nicht
	 *
	 * @param string $module
	 * @return boolean
	 */
	public static function isActive($module)
	{
		$info = self::parseInfo($module);
		return $info['active'] == 1 ? true : false;
	}
	/**
	 * Gibt ein alphabetisch sortiertes Array mit allen gefundenen
	 * Modulen des ACP3 mitsamt Modulinformationen aus
	 *
	 * @return array
	 */
	public static function modulesList()
	{
		static $mod_list = array();

		if (empty($mod_list)) {
			$uri_dir = scandir(MODULES_DIR);
			foreach ($uri_dir as $module) {
				$info = self::parseInfo($module);
				if (!empty($info))
					$mod_list[$info['name']] = $info;
			}
			ksort($mod_list);
		}
		return $mod_list;
	}
	/**
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		global $auth, $uri;

		if (!$auth->isUser() === true && defined('IN_ADM') === true && $uri->mod !== 'users' && $uri->file !== 'login') {
			$redirect_uri = base64_encode(substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1));
			$uri->redirect('acp/users/login/redirect_' . $redirect_uri);
		}

		switch (modules::check()) {
			// Seite ausgeben
			case 1:
				global $date, $db, $lang, $tpl;

				require MODULES_DIR . '' . $uri->mod . '/' . $uri->file . '.php';

				// Evtl. gesetzten Content-Type des Servers überschreiben
				header('Content-Type: ' . (defined('CUSTOM_CONTENT_TYPE') ? CUSTOM_CONTENT_TYPE : 'text/html') . '; charset=UTF-8');

				$tpl->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
				$tpl->assign('TITLE', breadcrumb::output(2));
				$tpl->assign('BREADCRUMB', breadcrumb::output());
				$tpl->assign('KEYWORDS', seo::getCurrentKeywordsOrDescription());
				$tpl->assign('DESCRIPTION', seo::getCurrentKeywordsOrDescription(2));
				$tpl->assign('CONTENT', !empty($content) ? $content : '');

				$minify = ROOT_DIR . 'includes/min/' . (CONFIG_SEO_MOD_REWRITE === true && defined('IN_ADM') === false ? '' : '?') . 'g=%s&amp;' . CONFIG_DESIGN;
				$tpl->assign('MIN_JAVASCRIPT', sprintf($minify, 'js'));
				$tpl->assign('MIN_STYLESHEET', sprintf($minify, 'css'));
				$tpl->assign('MIN_STYLESHEET_SIMPLE', sprintf($minify, 'css_simple'));

				// Falls ein Modul ein eigenes Layout verwenden möchte, dieses auch zulassen
				self::displayTemplate(defined('CUSTOM_LAYOUT') === true ? CUSTOM_LAYOUT : 'layout.tpl');
				break;
			// Kein Zugriff auf die Seite
			case 0:
				$uri->redirect('errors/403');
				break;
			// Seite nicht gefunden
			default:
				$uri->redirect('errors/404');
		}
	}
	/**
	 * Gibt ein Template direkt aus
	 *
	 * @param string $template
	 * @param mixed $cache_id
	 * @param integer $cache_lifetime
	 */
	public static function displayTemplate($template, $cache_id = null, $compile_id = null, $parent = null)
	{
		self::fetchTemplate($template, $cache_id, $compile_id, $parent, true);
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
	public static function fetchTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
	{
		global $lang, $tpl;

		if ($tpl->templateExists($template)) {
			return $tpl->fetch($template, $cache_id, $compile_id, $parent, $display);
		} elseif (defined('DEBUG') === true && DEBUG === true) {
			return sprintf($lang->t('errors', 'tpl_not_found'), $template);
		}

		return '';
	}
	/**
	 * Durchläuft für das angeforderte Modul den <info> Abschnitt in der
	 * module.xml und gibt die gefundenen Informationen als Array zurück
	 *
	 * @param string $module
	 * @return array
	 */
	public static function parseInfo($module)
	{
		static $parsed_modules = array();

		if (empty($parsed_modules)) {
			if (cache::check('modules_infos') === false)
				self::setModulesCache();
			$parsed_modules = cache::output('modules_infos');
		}
		return !empty($parsed_modules[$module]) ? $parsed_modules[$module] : array();
	}
	/**
	 * Setzt den Cache für alle vorliegenden Modulinformationen
	 */
	public static function setModulesCache()
	{
		$infos = array();
		$dirs = scandir(MODULES_DIR);
		foreach ($dirs as $dir) {
			if ($dir !== '.' && $dir !== '..' && is_file(MODULES_DIR . '/' . $dir . '/module.xml') === true) {
				$mod_info = xml::parseXmlFile(MODULES_DIR . '' . $dir . '/module.xml', 'info');

				if (is_array($mod_info)) {
					global $db, $lang;

					$infos[$dir] = array(
						'dir' => $dir,
						'active' => $db->countRows('*', 'modules', 'name = \'' . $db->escape($dir, 2) . '\' AND active = 1') == 1 ? true : false,
						'description' => isset($mod_info['description']['lang']) && $mod_info['description']['lang'] == 'true' ? $lang->t($dir, 'mod_description') : $mod_info['description']['lang'],
						'author' => $mod_info['author'],
						'version' => isset($mod_info['version']['core']) && $mod_info['version']['core'] == 'true' ? CONFIG_VERSION : $mod_info['version'],
						'name' => isset($mod_info['name']['lang']) && $mod_info['name']['lang'] == 'true' ? $lang->t($dir, $dir) : $mod_info['name'],
						'tables' => !empty($mod_info['tables']) ? explode(',', $mod_info['tables']) : false,
						'categories' => isset($mod_info['categories']) ? true : false,
						'js' => isset($mod_info['js']) ? true : false,
						'css' => isset($mod_info['css']) ? true : false,
						'protected' => isset($mod_info['protected']) ? true : false,
					);
				}
			}
		}
		cache::create('modules_infos', $infos);
	}
}