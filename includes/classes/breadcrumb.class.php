<?php
/**
 * Breadcrumbs
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

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
	 * @access private
	 */
	private $steps = array();

	public function __construct()
	{
		global $db, $lang, $uri;

		$module = $uri->mod;
		$file = $uri->file;

		// Frontendbereich
		if (defined('IN_ADM') === false) {
			$in = "'" . $uri->query . "', '" . $uri->mod . '/' . $uri->file . "/', '" . $uri->mod . "'";
			$pages = $db->query('SELECT p.title, p.uri, p.left_id, p.right_id FROM {pre}menu_items AS c, {pre}menu_items AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(' . $in . ') ORDER BY p.left_id DESC');
			$c_pages = count($pages);

			// Dynamische Seite (ACP3 intern)
			if ($c_pages > 0) {
				$left_id = $pages[0]['left_id'];
				$right_id = $pages[0]['right_id'];
				for ($i = $c_pages - 1; $i >= 0; --$i) {
					if ($left_id >= $pages[$i]['left_id'] && $right_id <= $pages[$i]['right_id']) {
						$this->assign($pages[$i]['title'], $uri->route($pages[$i]['uri'], 1));
					}
				}
			// Standardkrümelspur für den Frontendbereich erzeugen
			} elseif ($module !== 'static_pages') {
				$this->assign($file === 'list' ? $lang->t($module, $module) : $lang->t($module, $file), $uri->route($module . '/' . $file, 1));
			}
		// ACP
		} else {
			$this->assign($lang->t('common', 'acp'), $uri->route('acp'));
			if ($module !== 'errors') {
				if ($module !== 'acp') {
					$this->assign($lang->t($module, $module), $uri->route('acp/' . $module));
					if ($file !== 'adm_list')
						$this->assign($lang->t($module, $file));
				}
			} else {
				$this->assign($lang->t($module, $file));
			}
		}
	}
	/**
	 * Zuweisung der jewiligen Stufen der Brotkrümelspur
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $uri
	 * 	Der zum $title zugehörige Hyperlink
	 *
	 * @return array
	 */
	public function assign($title, $uri = 0)
	{
		static $i = 0;

		// Neue Brotkrume nur hinzufügen, falls noch keine mit dem gleichen Namen angelegt wurde
		if ($i === 0 || $this->steps[$i - 1]['title'] !== $title) {
			$this->steps[$i]['title'] = $title;
			$this->steps[$i]['uri'] = $uri;
			$this->steps[$i]['last'] = true;
			// Die vorherige Brotkrume ist nun nicht mehr das letzte Element
			if (isset($this->steps[$i - 1]))
				$this->steps[$i - 1]['last'] = false;
			++$i;
		}

		return $this;
	}
	/**
	 * Gibt je nach Modus entweder die Brotkrümelspur oder den Seitentitel aus
	 *
	 * @param integer $mode
	 * 	1 = Brotkrümelspur ausgeben
	 * 	2 = Nur Seitentitel ausgeben
	 *
	 * @return string
	 */
	public function output($mode = 1)
	{
		// Brotkrümelspur ausgeben
		if ($mode === 1) {
			global $tpl;

			$tpl->assign('breadcrumb', $this->steps);
			return $tpl->fetch('common/breadcrumb.tpl');
		// Nur Titel ausgeben
		} else {
			// Letzter Eintrag der Brotkrümelspur ist der Seitentitel
			return $this->steps[count($this->steps) - 1]['title'];
		}
	}
}