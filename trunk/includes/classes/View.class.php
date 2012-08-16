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
	 * Der auszugebende Inhalt der Seite
	 *
	 * @var string
	 */
	private static $content = '';
	/**
	 * Der auszugebende Content-Type der Seite
	 * @var string
	 */
	private static $content_type = 'Content-Type: text/html; charset=UTF-8';
	/**
	 * Das zuverwendende Seitenlayout
	 *
	 * @var string
	 */
	private static $layout = 'layout.tpl';

	/**
	 * Weist dem Template den auszugebenden Inhalt zu
	 *
	 * @param string $data
	 */
	public static function setContent($data)
	{
		self::$content = $data;
	}
	/**
	 * Weist der aktuell auszugebenden Seite den Content-Type zu
	 *
	 * @param string $data
	 */
	public static function setContentType($data)
	{
		self::$content_type = $data;
	}
	/**
	 * Weist der aktuell auszugebenden Seite ein Layout zu
	 *
	 * @param string $file
	 */
	public static function assignLayout($file)
	{
		self::$layout = $file;
	}
	/**
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		global $auth, $uri;

		if ($auth->isUser() === false && defined('IN_ADM') === true && $uri->mod !== 'users' && $uri->file !== 'login') {
			$redirect_uri = base64_encode('acp/' . $uri->query);
			$uri->redirect('users/login/redirect_' . $redirect_uri);
		}

		switch (ACP3_Modules::check()) {
			// Seite ausgeben
			case 1:
				global $breadcrumb, $date, $db, $lang, $session, $tpl;

				require MODULES_DIR . $uri->mod . '/' . $uri->file . '.php';

				// Evtl. gesetzten Content-Type des Servers überschreiben
				header(self::$content_type);

				if (self::$layout !== '') {
					$tpl->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
					$tpl->assign('TITLE', $breadcrumb->output(2));
					$tpl->assign('BREADCRUMB', $breadcrumb->output());
					$tpl->assign('META', ACP3_SEO::getMetaTags());
					$tpl->assign('CONTENT', self::$content);

					$minify = ROOT_DIR . 'includes/min/' . (CONFIG_SEO_MOD_REWRITE === true && defined('IN_ADM') === false ? '' : '?') . 'g=%s&amp;' . CONFIG_DESIGN;
					$layout = substr(self::$layout, 0, strpos(self::$layout, '.'));
					$tpl->assign('MIN_STYLESHEET', sprintf($minify, 'css') . ($layout !== 'layout' ? '&amp;layout=' . $layout : ''));
					$tpl->assign('MIN_JAVASCRIPT', sprintf($minify, 'js'));

					self::displayTemplate(self::$layout);
				} else {
					echo self::$content;
				}
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
		} else {
			// Pfad zerlegen
			$path = explode('/', $template);
			if (count($path) > 1 && $tpl->templateExists($path[0] . '/templates/' . $path[1])) {
				return $tpl->fetch($path[0] . '/templates/' . $path[1], $cache_id, $compile_id, $parent, $display);
			} else {
				return sprintf($lang->t('errors', 'tpl_not_found'), $template);
			}
		}
	}
}