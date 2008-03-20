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
	protected $steps = array();
	/**
	 * Das Ende der Brotkrümelspur
	 *
	 * @var string
	 * @access protected
	 */
	protected $end = '';

	/**
	 * Zuweisung der jewiligen Stufen der Brotkrümelspur
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $uri
	 * 	Der zum $title zugehörige Hyperlink
	 * @return array
	 */
	public function assign($title, $uri = 0)
	{
		static $i = 0;

		if (!empty($uri)) {
			$this->steps[$i]['uri'] = $uri;
			$this->steps[$i]['title'] = $title;
			$i++;
			return;
		} else {
			$this->end = $title;
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
	public function output($mode = 1)
	{
		global $modules, $tpl;

		$module = $modules->mod;
		$page = $modules->page;

		// Brotkrümelspur für das Frontend
		if (defined('IN_ACP3') && $mode == 1) {
			// Zusätzlich zugewiesene Brotkrumen an Smarty übergeben
			if (count($this->steps) > 0) {
				$tpl->assign('breadcrumb', $this->steps);
				$tpl->assign('end', $this->end);
			// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
			} else {
				if (!empty($this->end)) {
					$tpl->assign('end', $this->end);
				} elseif ($page == 'list') {
					$tpl->assign('end', lang($module, $module));
				} else {
					$tpl->assign('end', lang($module, $page));
				}
			}
			return $tpl->fetch('common/breadcrumb.html');
		// Brotkrümelspur für das Admin Panel
		} elseif (defined('IN_ADM') && $mode == 1) {
			if ($page == 'adm_list' && count($this->steps) == 0 && empty($this->end)) {
				$this->assign(lang('common', 'acp'), uri('acp'));
				$tpl->assign('breadcrumb', $this->steps);
				$tpl->assign('end', lang($module, $module));
			} elseif (count($this->steps) > 0 || !empty($this->end)) {
				$tpl->assign('breadcrumb', $this->steps);
				$tpl->assign('end', $this->end);
			// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
			} else {
				$this->assign(lang('common', 'acp'), uri('acp'));
				$this->assign(lang($module, $module), uri('acp/' . $module));
				$tpl->assign('breadcrumb', $this->steps);
				$tpl->assign('end', lang($module, $page));
			}
			return $tpl->fetch('common/breadcrumb.html');
		// Nur Seitentitel ausgeben
		} else {
			if (!empty($this->end)) {
				return $this->end;
			} else {
				return $page != 'list' && $page != 'adm_list' ? lang($module, $page) : lang($module, $module);
			}
		}
	}
}
?>