<?php
/**
 * Breadcrumbs
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Erzeugt die Brotkrümelspur und gibt den Titel der jeweiligen Seite aus
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class breadcrumb
{
	/**
	 * Enthält alle Schritte der Brotkrümelspur
	 *
	 * @var array
	 * @access protected
	 */
	protected static $steps = array();
	/**
	 * Das Ende der Brotkrümelspur
	 *
	 * @var string
	 * @access protected
	 */
	protected static $end = '';

	/**
	 * Zuweisung der jewiligen Stufen der Brotkrümelspur
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $uri
	 * 	Der zum $title zugehörige Hyperlink
	 * @return array
	 */
	public static function assign($title, $uri = 0)
	{
		static $i = 0;

		if (!empty($uri)) {
			self::$steps[$i]['uri'] = $uri;
			self::$steps[$i]['title'] = $title;
			$i++;
			return;
		} else {
			self::$end = $title;
			return;
		}
	}
	/**
	 * Gibt je nach Modus entweder die Brotkrümelspur oder den Seitentitel aus
	 *
	 * @param integer $mode
	 * 	1 = Brotkrümelspur ausgeben
	 * 	2 = Nur Seitentitel ausgeben
	 * @return string
	 */
	public static function output($mode = 1)
	{
		global $lang, $uri, $tpl;

		$module = $uri->mod;
		$page = $uri->page;

		// Brotkrümelspur für die Menüpunkte
		if ($module == 'pages' && $page == 'list') {
			global $db;

			$id = $uri->item;

			$chk_page = $db->query('SELECT COUNT(p.id) AS level FROM ' . CONFIG_DB_PRE . 'pages p, ' . CONFIG_DB_PRE . 'pages c WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.id = ' . $uri->item . ' AND p.mode = 2 ORDER BY p.left_id DESC LIMIT 1', 1);

			// Dynamische Seiten (ACP3 intern)
			if ($chk_page == 1 && !empty(self::$steps) && !empty(self::$end)) {
				// Die durch das Modul festgelegte Brotkrümelspur ausgeben
				if ($mode == 1) {
					$tpl->assign('breadcrumb', self::$steps);
					$tpl->assign('end', self::$end);
					return $tpl->fetch('common/breadcrumb.html');
				// Nur den Titel der Moduldatei ausgeben
				} else {
					return self::$end;
				}
			// Statische Seiten
			} else {
				$pages = $db->query('SELECT p.id, p.title FROM ' . CONFIG_DB_PRE . 'pages p, ' . CONFIG_DB_PRE . 'pages c WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.id = ' . $uri->item . ' ORDER BY p.left_id');
				$c_pages = count($pages);

				// Brotkrümelspur ausgeben
				if ($mode == 1) {
					for ($i = 0; $i < $c_pages; ++$i) {
						if ($i == $c_pages - 1) {
							self::$end = $pages[$i]['title'];
						} else {
							self::assign($pages[$i]['title'], uri('pages/list/item_' . $pages[$i]['id']));
						}
					}
					$tpl->assign('breadcrumb', self::$steps);
					$tpl->assign('end', self::$end);
					return $tpl->fetch('common/breadcrumb.html');
				// Nur Seitentitel ausgeben
				} else {
					return $pages[$c_pages - 1]['title'];
				}
			}
		// Brotkrümelspur für das Frontend
		} elseif (defined('IN_ACP3') && $mode == 1) {
			// Zusätzlich zugewiesene Brotkrumen an Smarty übergeben
			if (count(self::$steps) > 0) {
				$tpl->assign('breadcrumb', self::$steps);
				$tpl->assign('end', self::$end);
			// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
			} else {
				if (!empty(self::$end)) {
					$tpl->assign('end', self::$end);
				} elseif ($page == 'list') {
					$tpl->assign('end', $lang->t($module, $module));
				} else {
					$tpl->assign('end', $lang->t($module, $page));
				}
			}
			return $tpl->fetch('common/breadcrumb.html');
		// Brotkrümelspur für das Admin Panel
		} elseif (defined('IN_ADM') && $mode == 1) {
			if ($page == 'adm_list' && count(self::$steps) == 0 && empty(self::$end)) {
				self::assign($lang->t('common', 'acp'), uri('acp'));
				$tpl->assign('breadcrumb', self::$steps);
				$tpl->assign('end', $lang->t($module, $module));
			} elseif (count(self::$steps) > 0 || !empty(self::$end)) {
				$tpl->assign('breadcrumb', self::$steps);
				$tpl->assign('end', self::$end);
			// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
			} else {
				self::assign($lang->t('common', 'acp'), uri('acp'));
				self::assign($lang->t($module, $module), uri('acp/' . $module));
				$tpl->assign('breadcrumb', self::$steps);
				$tpl->assign('end', $lang->t($module, $page));
			}
			return $tpl->fetch('common/breadcrumb.html');
		// Nur Seitentitel ausgeben
		} else {
			if (!empty(self::$end)) {
				return self::$end;
			} else {
				return $page != 'list' && $page != 'adm_list' ? $lang->t($module, $page) : $lang->t($module, $module);
			}
		}
	}
}
?>