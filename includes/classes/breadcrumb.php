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
	function assign($title, $uri = 0)
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
	function output($mode = 1)
	{
		global $modules, $tpl;

		// Brotkrümelspur für das Frontend
		if (defined('IN_ACP3') && $mode == 1) {
			// Zusätzlich zugewiesene Brotkrumen holen und Einträge zählen
			$c_steps = count($this->steps);
			if ($c_steps > 0) {
				$tpl->assign('breadcrumb', $this->steps);
				$tpl->assign('end', $this->end);
				// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
			} else {
				if (!empty($this->end)) {
					$tpl->assign('end', $this->end);
				} elseif ($modules->page == 'list' || $modules->page == 'entry') {
					$tpl->assign('end', lang($modules->mod, $modules->mod));
				} else {
					$tpl->assign('end', lang($modules->mod, $modules->page));
				}
			}
			return $tpl->fetch('common/breadcrumb.html');
			// Brotkrümelspur für das Admin Panel
		} elseif (defined('IN_ADM') && $mode == 1) {
			// Ausgangsstufe der Brotkrümelspur
			$breadcrumb[0]['uri'] = uri('acp');
			$breadcrumb[0]['title'] = lang('common', 'acp');

			// Zusätzlich zugewiesene Brotkrumen holen und Einträge zählen
			$c_steps = count($this->steps);

			if (($modules->page == 'adm_list' || $modules->page == 'entry') && $c_steps == 0) {
				$tpl->assign('end', lang($modules->mod, $modules->mod));
			} else {
				if ($c_steps > 0) {
					$breadcrumb = array_merge($breadcrumb, $this->steps);
					$tpl->assign('end', $this->end);
					// Falls keine zusätzlichen Brotkrumen angegeben sind, jeweiligen Seitennamen der Moduldatei ausgeben
				} else {
					$breadcrumb[1]['uri'] = uri('acp/' . $modules->mod);
					$breadcrumb[1]['title'] = lang($modules->mod, $modules->mod);
					$tpl->assign('end', lang($modules->mod, $modules->page));
				}
			}
			$tpl->assign('breadcrumb', $breadcrumb);
			return $tpl->fetch('common/breadcrumb.html');
			// Nur Seitentitel ausgeben
		} elseif ($mode == 2) {
			if (!empty($this->end)) {
				return $this->end;
			} else {
				return $modules->page != 'list' && $modules->page != 'adm_list' && $modules->page != 'entry' ? lang($modules->mod, $modules->page) : lang($modules->mod, $modules->mod);
			}
		}
		return '';
	}
}
?>