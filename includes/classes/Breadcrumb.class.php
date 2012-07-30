<?php
/**
 * Breadcrumbs
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Erzeugt die Brotkrümelspur und gibt den Titel der jeweiligen Seite aus
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_Breadcrumb
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
		global $db, $uri;

		// Frontendbereich
		if (defined('IN_ADM') === false) {
			$in = "'" . $uri->query . "', '" . $uri->getCleanQuery() . "', '" . $uri->mod . '/' . $uri->file . "/', '" . $uri->mod . "'";
			$pages = $db->query('SELECT p.title, p.uri, p.left_id, p.right_id FROM {pre}menu_items AS c, {pre}menu_items AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(' . $in . ') ORDER BY p.left_id DESC');
			$c_pages = count($pages);

			// Dynamische Seite (ACP3 intern)
			if ($c_pages > 0) {
				for ($i = $c_pages - 1; $i >= 0; --$i) {
					if ($pages[0]['left_id'] >= $pages[$i]['left_id'] && $pages[0]['right_id'] <= $pages[$i]['right_id']) {
						$this->append($pages[$i]['title'], $uri->route($pages[$i]['uri'], 1));
					}
				}
			}
		}
	}
	/**
	 * Zuweisung der jeweiligen Stufen der Brotkrümelspur
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $path
	 * 	Die zum $title zugehörige ACP3-interne URI
	 * @return \bACP3_Breadcrumb	 */
	public function append($title, $path = 0)
	{
		static $i = 0;

		// Neue Brotkrume nur hinzufügen, falls noch keine mit dem gleichen Namen angelegt wurde
		if ($i === 0 || $this->searchForDuplicates($title, $path) === false) {
			$this->steps[$i]['title'] = $title;
			$this->steps[$i]['uri'] = $path;
			$this->steps[$i]['last'] = true;
			// Die vorherige Brotkrume ist nun nicht mehr das letzte Element
			if (isset($this->steps[$i - 1]))
				$this->steps[$i - 1]['last'] = false;
			++$i;
		}

		return $this;
	}
	/**
	 * Fügt Brotkrumen an den Anfang an
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $path
	 * 	Die zum $title zugehörige ACP3-interne URI
	 * @return \bACP3_Breadcrumb
	 */
	private function prepend($title, $path)
	{
		$step = array(
			'title' => $title,
			'uri' => $path,
			'last' => false,
		);
		array_unshift($this->steps, $step);

		return $this;
	}
	/**
	 * Ersetzt die aktuell letzte Brotkrume mit neuen Werten
	 * 
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $path
	 * 	Die zum $title zugehörige ACP3-interne URI
	 * @return \bACP3_Breadcrumb
	 */
	public function replaceAnchestor($title, $path = 0)
	{
		$index = count($this->steps) - (!empty($this->steps) ? 1 : 0);
		$this->steps[$index]['title'] = $title;
		$this->steps[$index]['uri'] = $path;
		$this->steps[$index]['last'] = true;

		return $this;
	}
	/**
	 * Sucht nach bereits vorhandenen Brotkrumen, damit keine Dopplungen auftreten
	 *
	 * @param string $title
	 *	Der zu überprüfende Seitentitel
	 * @param string $path
	 *	Die zu überprüfende ACP3-interne URI
	 * @return boolean 
	 */
	private function searchForDuplicates($title, $path)
	{
		if (!empty($this->steps)) {
			foreach ($this->steps as $row) {
				if ($row['title'] === $title || $row['uri'] === $path)
					return true;
			}
		}
		return false;
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
		global $lang, $tpl, $uri;

		$module = $uri->mod;
		$file = $uri->file;

		// Brotkrümelspur für das Admin-Panel
		if (defined('IN_ADM') === true) {
			// Wenn noch keine Brotkrümelspur gesetzt ist, dies nun tun
			if (empty($this->steps)) {
				$this->append($lang->t('common', 'acp'), $uri->route('acp'));
				if ($module !== 'errors') {
					if ($module !== 'acp') {
						$this->append($lang->t($module, $module), $uri->route('acp/' . $module));
						if ($file !== 'adm_list')
							$this->append($lang->t($module, $file));
					}
				} else {
					$this->append($lang->t($module, $file));
				}
			// Falls bereits Stufen gesetzt wurden, Links für das Admin-Panel und
			// die Modulverwaltung in ungedrehter Reihenfolge voranstellen
			} elseif ($this->searchForDuplicates($lang->t('common', 'acp'), $uri->route('acp')) === false) {
				if ($module !== 'acp')
					$this->prepend($lang->t($module, $module), $uri->route('acp/' . $module));
				$this->prepend($lang->t('common', 'acp'), $uri->route('acp'));
			}
		// Falls noch keine Brotkrümelspur gesetzt sein sollte, dies nun tun
		} elseif (empty($this->steps)) {
			$this->append($file === 'list' ? $lang->t($module, $module) : $lang->t($module, $file), $uri->route($module . '/' . $file, 1));
		}

		// Brotkrümelspur ausgeben
		if ($mode === 1) {
			$tpl->assign('breadcrumb', $this->steps);
			return $tpl->fetch('common/breadcrumb.tpl');
		// Nur Titel ausgeben
		} else {
			// Letzter Eintrag der Brotkrümelspur ist der Seitentitel
			return $this->steps[count($this->steps) - 1]['title'];
		}
	}
}