<?php

/**
 * Breadcrumbs
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

/**
 * Erzeugt die Brotkrümelspur und gibt den Titel der jeweiligen Seite aus
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_Breadcrumb {

	/**
	 * Enthält alle Schritte der Brotkrümelspur,
	 * welche sich aus der Navigationsstruktur der Website ergeben
	 *
	 * @var array
	 * @access private
	 */
	private $steps_db = array();
	/**
	 * Enthält alle Schritte der Brotkrümelspur,
	 * welche von den Modulen festgelegt werden
	 *
	 * @var array
	 * @access private
	 */
	private $steps_mods = array();

	/**
	 * @var array
	 */
	private $title = array('separator' => '-', 'prefix' => '', 'postfix' => '');

	/**
	 * Enthält die gecachete Brotkrümelspur
	 *
	 * @var array
	 */
	private $breadcrumb_cache = array();

	public function __construct()
	{
		// Frontendbereich
		if (defined('IN_ADM') === false) {
			$in = array(ACP3_CMS::$uri->query, ACP3_CMS::$uri->getCleanQuery(), ACP3_CMS::$uri->mod . '/' . ACP3_CMS::$uri->file . '/', ACP3_CMS::$uri->mod);
			$items = ACP3_CMS::$db2->executeQuery('SELECT p.title, p.uri, p.left_id, p.right_id FROM ' . DB_PRE . 'menu_items AS c, ' . DB_PRE . 'menu_items AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(?) GROUP BY p.uri ORDER BY p.left_id ASC', array($in), array(\Doctrine\DBAL\Connection::PARAM_STR_ARRAY))->fetchAll();
			$c_items = count($items);

			// Dynamische Seite (ACP3 intern)
			for ($i = 0; $i < $c_items; ++$i) {
				$this->appendFromDB($items[$i]['title'], ACP3_CMS::$uri->route($items[$i]['uri']));
			}
		}
	}

	/**
	 * 
	 * @param string $value
	 */
	public function setTitleSeparator($value)
	{
		$this->title['separator'] = $value;

		return $this;
	}

	/**
	 * 
	 * @param string $value
	 */
	public function setTitlePrefix($value)
	{
		$this->title['prefix'] = $value;

		return $this;
	}

	/**
	 * 
	 * @param string $value
	 */
	public function setTitlePostfix($value)
	{
		$this->title['postfix'] = $value;

		return $this;
	}

	/**
	 * Zuweisung einer neuen Stufe zur Brotkrümelspur
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $path
	 * 	Die zum $title zugehörige ACP3-interne URI
	 * @return \bACP3_Breadcrumb
	 */
	private function appendFromDB($title, $path = 0)
	{
		$this->steps_db[] = array(
			'title' => $title,
			'uri' => $path
		);

		return $this;
	}

	/**
	 * Zuweisung einer neuen Stufe zur Brotkrümelspur
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $path
	 * 	Die zum $title zugehörige ACP3-interne URI
	 * @return \bACP3_Breadcrumb
	 */
	public function append($title, $path = 0)
	{
		$this->steps_mods[] = array(
			'title' => $title,
			'uri' => $path
		);

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
		);
		array_unshift($this->steps_mods, $step);
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
	public function replaceAnchestor($title, $path = 0, $db_steps = false)
	{
		if ($db_steps === true) {
			$index = count($this->steps_db) - (!empty($this->steps_db) ? 1 : 0);
			$this->steps_db[$index]['title'] = $title;
			$this->steps_db[$index]['uri'] = $path;
		} else {
			$index = count($this->steps_mods) - (!empty($this->steps_mods) ? 1 : 0);
			$this->steps_mods[$index]['title'] = $title;
			$this->steps_mods[$index]['uri'] = $path;
		}

		return $this;
	}

	/**
	 * Gibt je nach Modus entweder die Brotkrümelspur oder den Seitentitel aus
	 *
	 * @param integer $mode
	 * 	1 = Brotkrümelspur ausgeben
	 * 	2 = Nur Seitentitel ausgeben
	 *  3 = Seitentitel mit eventuellen Prefixes und Postfixes ausgeben
	 * @return string
	 */
	public function output($mode = 1)
	{
		$module = ACP3_CMS::$uri->mod;
		$file = ACP3_CMS::$uri->file;

		if (empty($this->breadcrumb_cache)) {
			// Brotkrümelspur für das Admin-Panel
			if (defined('IN_ADM') === true) {
				if ($module !== 'acp')
					$this->setTitlePostfix(ACP3_CMS::$lang->t('system', 'acp'));

				// Wenn noch keine Brotkrümelspur gesetzt ist, dies nun tun
				if (empty($this->steps_mods)) {
					$this->append(ACP3_CMS::$lang->t('system', 'acp'), ACP3_CMS::$uri->route('acp'));
					if ($module !== 'errors') {
						if ($module !== 'acp') {
							$this->append(ACP3_CMS::$lang->t($module, $module), ACP3_CMS::$uri->route('acp/' . $module));
							if ($file !== 'acp_list')
								$this->append(ACP3_CMS::$lang->t($module, $file), ACP3_CMS::$uri->route('acp/' . $module . '/' . $file));
						}
					} else {
						$this->append(ACP3_CMS::$lang->t($module, $file), ACP3_CMS::$uri->route('acp/' . $module . '/' . $file));
					}
				// Falls bereits Stufen gesetzt wurden, Links für das Admin-Panel und
				// die Modulverwaltung in umgedrehter Reihenfolge voranstellen
				} else {
					if ($module !== 'acp')
						$this->prepend(ACP3_CMS::$lang->t($module, $module), ACP3_CMS::$uri->route('acp/' . $module));
					$this->prepend(ACP3_CMS::$lang->t('system', 'acp'), ACP3_CMS::$uri->route('acp'));
				}
				$this->breadcrumb_cache = $this->steps_mods;
			// Brotkrümelspur für das Frontend
			} else {
				if (empty($this->steps_db) && empty($this->steps_mods)) {
					$this->append($file === 'list' ? ACP3_CMS::$lang->t($module, $module) : ACP3_CMS::$lang->t($module, $file), ACP3_CMS::$uri->route($module . '/' . $file));
					$this->breadcrumb_cache = $this->steps_mods;
				} elseif (!empty($this->steps_db) && empty($this->steps_mods)) {
					$this->breadcrumb_cache = $this->steps_db;
				} elseif (!empty($this->steps_mods) && empty($this->steps_db)) {
					$this->breadcrumb_cache = $this->steps_mods;
				} else {
					$this->breadcrumb_cache = $this->steps_db;

					if ($this->breadcrumb_cache[count($this->breadcrumb_cache) - 1]['uri'] === $this->steps_mods[0]['uri']) {
						$c_steps_mods = count($this->steps_mods);
						for ($i = 1; $i < $c_steps_mods; ++$i) {
							$this->breadcrumb_cache[] = $this->steps_mods[$i];
						}
					}
				}
			}

			// Letzte Brotkrume markieren
			$this->breadcrumb_cache[count($this->breadcrumb_cache) - 1]['last'] = true;
		}

		// Brotkrümelspur ausgeben
		if ($mode === 1) {
			ACP3_CMS::$view->assign('breadcrumb', $this->breadcrumb_cache);
			return ACP3_CMS::$view->fetchTemplate('system/breadcrumb.tpl');
		// Nur Titel ausgeben
		} else {
			// Letzter Eintrag der Brotkrümelspur ist der Seitentitel
			$title = $this->breadcrumb_cache[count($this->breadcrumb_cache) - 1]['title'];
			if ($mode === 3) {
				$separator = ' ' . $this->title['separator'] . ' ';
				if (!empty($this->title['prefix']))
					$title = $this->title['prefix'] . $separator . $title;
				if (!empty($this->title['postfix']))
					$title.= $separator . $this->title['postfix'];
				$title.= $separator . CONFIG_SEO_TITLE;
			}
			return $title;
		}
	}
}