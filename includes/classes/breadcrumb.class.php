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
	/**
	 * Das Ende der Brotkrümelspur
	 *
	 * @var string
	 * @access private
	 */
	private $end = '';

	public function __construct()
	{
		global $db, $lang, $uri;

		$module = $uri->mod;
		$file = $uri->file;

		// Frontendbereich
		if (defined('IN_ADM') === false) {
			$pages = $db->query('SELECT p.title, p.uri, a.alias FROM {pre}menu_items AS c, {pre}menu_items AS p LEFT JOIN {pre}seo AS a ON(a.uri = p.uri) WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri = \'' . $db->escape($uri->query) . '\' GROUP BY p.uri ORDER BY p.left_id ASC');
			$c_pages = count($pages);

			// Dynamische Seite (ACP3 intern)
			if ($c_pages > 1) {
				// Die durch das Modul festgelegte Brotkrümelspur mit den
				// übergeordneten Menüpunkten verschmelzen
				if (!empty($this->steps) && !empty($this->end)) {
					unset($this->steps[0]);
					for ($i = $c_pages - 1; $i >= 0; --$i) {
						$pages[$i]['uri'] = $uri->route(!empty($pages[$i]['alias']) ? $pages[$i]['alias'] : $pages[$i]['uri']);
						array_unshift($this->steps, $pages[$i]);
					}
				} else {
					for ($i = 0; $i < $c_pages; ++$i) {
						$pages[$i]['uri'] = $uri->route(!empty($pages[$i]['alias']) ? $pages[$i]['alias'] : $pages[$i]['uri']);
					}
					$this->steps = array_slice($pages, 0, -1);
					$this->assign($pages[$c_pages - 1]['title']);
				}
			// Brotkümelspur erzeugen, falls keine durch das Modul festgelegt wurde
			} elseif (empty($this->steps) && empty($this->end)) {
				$this->end = $file == 'list' ? $lang->t($module, $module) : $lang->t($module, $file);
			}
		// ACP
		} else {
			$this->assign($lang->t('common', 'acp'), $uri->route('acp'));
			// Modulindex der jeweiligen ACP-Seite
			if ($file == 'adm_list') {
				$this->assign($lang->t($module, $module));
			} elseif ($module == 'errors') {
				$this->assign($lang->t($module, $file));
			} else {
				$this->assign($lang->t($module, $module), $uri->route('acp/' . $module));
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

		if (!empty($uri)) {
			$this->steps[$i]['title'] = $title;
			$this->steps[$i]['uri'] = $uri;
			++$i;
		} else {
			$this->end = $title;
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
			$tpl->assign('end', $this->end);
			return $tpl->fetch('common/breadcrumb.tpl');
		}

		// Nur Titel ausgeben
		return $this->end;
	}
}