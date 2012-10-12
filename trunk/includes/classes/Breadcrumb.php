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
	 * Enthält alle Schritte der Brotkrümelspur
	 *
	 * @var array
	 * @access private
	 */
	private $steps = array();

	/**
	 *
	 * @var array
	 */
	private $title = array('separator' => '-', 'prefix' => '', 'postfix' => '');

	public function __construct()
	{
		// Frontendbereich
		if (defined('IN_ADM') === false) {
			$in = array(ACP3_CMS::$uri->query, ACP3_CMS::$uri->getCleanQuery(), ACP3_CMS::$uri->mod . '/' . ACP3_CMS::$uri->file . '/', ACP3_CMS::$uri->mod);
			$items = ACP3_CMS::$db2->executeQuery('SELECT p.title, p.uri, p.left_id, p.right_id FROM ' . DB_PRE . 'menu_items AS c, ' . DB_PRE . 'menu_items AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(?) ORDER BY p.left_id ASC', array($in), array(\Doctrine\DBAL\Connection::PARAM_STR_ARRAY))->fetchAll();
			$c_items = count($items);

			// Dynamische Seite (ACP3 intern)
			for ($i = 0; $i < $c_items; ++$i) {
				$this->append($items[$i]['title'], ACP3_CMS::$uri->route($items[$i]['uri']));
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
	 * Zuweisung der jeweiligen Stufen zur Brotkrümelspur
	 *
	 * @param string $title
	 * 	Bezeichnung der jeweiligen Stufe der Brotkrume
	 * @param string $path
	 * 	Die zum $title zugehörige ACP3-interne URI
	 * @return \bACP3_Breadcrumb
	 */
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
		if ($this->searchForDuplicates($title, $path) === false) {
			$step = array(
				'title' => $title,
				'uri' => $path,
				'last' => false,
			);
			array_unshift($this->steps, $step);
		}

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
	 * 	Der zu überprüfende Seitentitel
	 * @param string $path
	 * 	Die zu überprüfende ACP3-interne URI
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
		$module = ACP3_CMS::$uri->mod;
		$file = ACP3_CMS::$uri->file;

		// Brotkrümelspur für das Admin-Panel
		if (defined('IN_ADM') === true) {
			// Wenn noch keine Brotkrümelspur gesetzt ist, dies nun tun
			if (empty($this->steps)) {
				$this->append(ACP3_CMS::$lang->t('system', 'acp'), ACP3_CMS::$uri->route('acp'));
				if ($module !== 'errors') {
					if ($module !== 'acp') {
						$this->append(ACP3_CMS::$lang->t($module, $module), ACP3_CMS::$uri->route('acp/' . $module));
						if ($file !== 'acp_list')
							$this->append(ACP3_CMS::$lang->t($module, $file));
					}
				} else {
					$this->append(ACP3_CMS::$lang->t($module, $file));
				}
			// Falls bereits Stufen gesetzt wurden, Links für das Admin-Panel und
			// die Modulverwaltung in ungedrehter Reihenfolge voranstellen
			} else {
				if ($module !== 'acp')
					$this->prepend(ACP3_CMS::$lang->t($module, $module), ACP3_CMS::$uri->route('acp/' . $module));
				$this->prepend(ACP3_CMS::$lang->t('system', 'acp'), ACP3_CMS::$uri->route('acp'));
			}
		// Falls noch keine Brotkrümelspur gesetzt sein sollte, dies nun tun
		} elseif (empty($this->steps)) {
			$this->append($file === 'list' ? ACP3_CMS::$lang->t($module, $module) : ACP3_CMS::$lang->t($module, $file), ACP3_CMS::$uri->route($module . '/' . $file));
		// Der Modulunterseite den richtigen Seitentitel zuweisen
		} elseif ($this->steps[count($this->steps) - 1]['uri'] !== ACP3_CMS::$uri->route(ACP3_CMS::$uri->query)) {
			$this->replaceAnchestor(ACP3_CMS::$lang->t($module, $file), ACP3_CMS::$uri->route(ACP3_CMS::$uri->query));
		}

		// Brotkrümelspur ausgeben
		if ($mode === 1) {
			ACP3_CMS::$view->assign('breadcrumb', $this->steps);
			return ACP3_CMS::$view->fetchTemplate('system/breadcrumb.tpl');
		// Nur Titel ausgeben
		} else {
			// Letzter Eintrag der Brotkrümelspur ist der Seitentitel
			$title = $this->steps[count($this->steps) - 1]['title'];
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