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
	public static function output($mode = 1, $id = '')
	{
		global $uri, $tpl;

		$module = $uri->mod;
		$page = $uri->page;

		// Brotkrümelspur für die Menüpunkte
		if ($module == 'pages' && $page == 'list') {
			global $db;

			if (!validate::isNumber($id))
				$id = $uri->item;

			$page = $db->select('mode, parent, title', 'pages', 'id = \'' . $id . '\' AND (mode = \'1\' OR mode = \'2\')');

			// Dynamische Seiten (ACP3 intern)
			if ($id == $uri->item && $page[0]['mode'] == 2 && empty($page[0]['parent']) && !empty(self::$steps) && self::$end != '') {
				if ($mode == 1) {
					$tpl->assign('breadcrumb', self::$steps);
					$tpl->assign('end', self::$end);
					return $tpl->fetch('common/breadcrumb.html');
				} else {
					return self::$end;
				}
			// Statische Seiten
			} else {
				// Brotkrümelspur ausgeben
				if ($mode == 1) {
					if ($id == $uri->item) {
						self::$steps = array();
						self::$end = '';
					}
					if (empty(self::$end)) {
						self::$end = $page[0]['title'];
					}
					if ($db->select('parent', 'pages', 'id = \'' . $page[0]['parent'] . '\' AND (mode = \'1\' OR mode = \'2\')', 0, 0, 0, 1) > 0) {
						$parent = $db->select('title', 'pages', 'id = \'' . $page[0]['parent'] . '\' AND (mode = \'1\' OR mode = \'2\')');
						self::assign($parent[0]['title'], uri('pages/list/item_' . $page[0]['parent']));

						return self::output(1, $page[0]['parent']);
					}
					$pages = self::$steps;
					krsort($pages);
					$tpl->assign('breadcrumb', $pages);
					$tpl->assign('end', self::$end);
					return $tpl->fetch('common/breadcrumb.html');
				// Nur Seitentitel ausgeben
				} else {
					return $page[0]['title'];
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
					$tpl->assign('end', lang($module, $module));
				} else {
					$tpl->assign('end', lang($module, $page));
				}
			}
			return $tpl->fetch('common/breadcrumb.html');
		// Brotkrümelspur für das Admin Panel
		} elseif (defined('IN_ADM') && $mode == 1) {
			if ($page == 'adm_list' && count(self::$steps) == 0 && empty(self::$end)) {
				self::assign(lang('common', 'acp'), uri('acp'));
				$tpl->assign('breadcrumb', self::$steps);
				$tpl->assign('end', lang($module, $module));
			} elseif (count(self::$steps) > 0 || !empty(self::$end)) {
				$tpl->assign('breadcrumb', self::$steps);
				$tpl->assign('end', self::$end);
			// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
			} else {
				self::assign(lang('common', 'acp'), uri('acp'));
				self::assign(lang($module, $module), uri('acp/' . $module));
				$tpl->assign('breadcrumb', self::$steps);
				$tpl->assign('end', lang($module, $page));
			}
			return $tpl->fetch('common/breadcrumb.html');
		// Nur Seitentitel ausgeben
		} else {
			if (!empty(self::$end)) {
				return self::$end;
			} else {
				return $page != 'list' && $page != 'adm_list' ? lang($module, $page) : lang($module, $module);
			}
		}
	}
}
?>