<?php
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
	 * Das Ende der Brotkrpmelspur
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
	function assign($title, $uri = 0) {
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
	function output($mode = 1) {
		global $modules, $tpl;

		// Brotkrümelspur für das Frontend
		if (defined('IN_ACP3')) {
			// Brotkrümelspur ausgeben
			if ($mode == 1) {
				// Zusätzlich zugewiesene Brotkrumen holen und Einträge zählen
				$c_steps = count($this->steps);
				if ($c_steps > 0) {
					$tpl->assign('breadcrumb', $this->steps);
					$tpl->assign('end', $this->end);
					// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
				} else {
					if ($modules->page == 'list' || $modules->page == 'entry') {
						$tpl->assign('end', lang($modules->mod, $modules->mod));
					} else {
						$tpl->assign('end', lang($modules->mod, $modules->page));
					}
				}
				return $tpl->fetch('common/breadcrumb.html');
				// Nur Seitentitel ausgeben
			} else {
				return $modules->page != 'list' && $modules->page != 'entry' ? lang($modules->mod, $modules->page) : lang($modules->mod, $modules->mod);
			}
			// Brotkrümelspur für das Admin Panel
		} elseif (defined('IN_ADM')) {
			// Ausgangsstufe der Brotkrümelspur
			$breadcrumb[0]['uri'] = uri('acp');
			$breadcrumb[0]['title'] = lang('common', 'acp');
			// Brotkrümelspur ausgeben
			if ($mode == 1) {
				if ($modules->mod == 'errors' || $modules->page == 'adm_list' || $modules->page == 'entry') {
					$tpl->assign('end', lang($modules->mod, $modules->mod));
				} else {
					$breadcrumb[1]['uri'] = uri('acp/' . $modules->mod);
					$breadcrumb[1]['title'] = lang($modules->mod, $modules->mod);
					$tpl->assign('end', lang($modules->mod, $modules->page));
				}
				$tpl->assign('breadcrumb', $breadcrumb);
				return $tpl->fetch('common/breadcrumb.html');
			}
			// Nur Seitentitel ausgeben
			else {
				return $modules->mod == 'errors' || $modules->page == 'adm_list' || $modules->page == 'entry' ? lang($modules->mod, $modules->mod) : lang($modules->mod, $modules->page);
			}
		}
		return '';
	}
}
?>