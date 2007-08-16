<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Auflistung der übergeordneten Seiten
 *
 * @param integer $id
 * @param integer $parent
 * @return array
 */
function pages_list($id = 0, $parent = 0)
{
	global $db;
	static $output = array(), $key = 0, $spaces = '';

	$pages = $db->select('id, title', 'pages', 'mode = \'1\' AND parent = \'' . $id . '\'', 'block_id ASC, sort ASC, title ASC');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		if ($id != 0)
			$spaces.= '&nbsp;&nbsp;';

		for ($i = 0; $i < $c_pages; $i++) {
			$output[$key]['id'] = $pages[$i]['id'];
			$output[$key]['selected'] = select_entry('parent', $pages[$i]['id'], $parent);
			$output[$key]['title'] = $spaces . $pages[$i]['title'];
			$key++;

			pages_list($pages[$i]['id'], $parent);

			if ($i == $c_pages - 1) {
				$spaces = substr($spaces, 0, -12);
			}
		}
	}
	return $output;
}
/**
 * Überprüfung der übergeordneten Seite, damit keine Endlosschleifen entstehen
 *
 * @param integer $id
 * @param integer $parent_id
 * @return boolean
 */
function parent_check($id, $parent_id)
{
	global $db;

	$parents = $db->select('parent', 'pages', 'id = \'' . $parent_id . '\'');
	$c_parents = count($parents);

	if ($c_parents > 0) {
		for ($i = 0; $i < $c_parents; $i++) {
			if ($parents[$i]['parent'] == $id)
				return true;

			if ($db->select('id', 'pages', 'id = \'' . $parents[$i]['parent'] . '\'', 0, 0, 0, 1) > 0)
				parent_check($id, $parents[$i]['parent']);
		}
	}
	return false;
}
/**
 * Verarbeitet die Navigationsleiste und selektiert die aktuelle Seite,
 * falls diese sich ebenfalls in der Navigationsleiste befindet
 *
 * @return mixed
 */
function process_navbar()
{
	global $cache, $db, $modules;

	if (!$cache->check('pages')) {
		$cache->create('pages', $db->select('p.id, p.start, p.end, p.mode, p.title, p.uri, p.target, b.index_name AS block_name', 'pages AS p, ' . CONFIG_DB_PRE . 'pages_blocks AS b', 'p.block_id != \'0\' AND p.block_id = b.id', 'p.sort ASC, p.title ASC'));
	}
	$pages = $cache->output('pages');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		$navbar = array();
		$selected = ' class="selected"';
		for ($i = 0; $i < $c_pages; $i++) {
			if ($pages[$i]['start'] == $pages[$i]['end'] && $pages[$i]['start'] <= date_aligned(2, time()) || $pages[$i]['start'] != $pages[$i]['end'] && $pages[$i]['start'] <= date_aligned(2, time()) && $pages[$i]['end'] >= date_aligned(2, time())) {
				switch ($pages[$i]['mode']) {
					case '1':
						$link['href'] = uri('pages/list/id_' . $pages[$i]['id']);
						$link['selected'] = $modules->mod == 'pages' && $modules->page == 'list' && $modules->id == $pages[$i]['id'] ? $selected : '';
						break;
					case '2':
						// URL zum Parsen in ein Array zerlegen
						$uri_arr = explode('/', $pages[$i]['uri']);

						$link['href'] = uri($pages[$i]['uri']);
						$link['selected'] = '';

						// Genaue Überprüfung der URL, damit nicht 2 Menüpunkte selektiert werden (passiert nur noch bei identischen URLs)
						if (!empty($uri_arr[2]) && $modules->mod == $uri_arr[0] && $modules->page == $uri_arr[1] && (!empty($modules->id) || !empty($modules->cat) || !empty($modules->action) || !empty($modules->gen))) {
							$c_uri_arr = count($uri_arr);
							for ($j = 2; $j < $c_uri_arr; $j++) {
								if (!empty($uri_arr[$j])) {
									$is_page = false;
									if (ereg('^(id_[0-9]+)$', $uri_arr[$j]) && str_replace('id_', '', $uri_arr[$j]) == $modules->id ||
										ereg('^(cat_[0-9]+)$', $uri_arr[$j]) && str_replace('cat_', '', $uri_arr[$j]) == $modules->cat ||
										ereg('^(action_[_a-z0-9-]+)$', $uri_arr[$j]) && str_replace('action_', '', $uri_arr[$j]) == $modules->action) {
										$is_page = true;
									} elseif (ereg('^([_a-z0-9-]+)_(.+)$', $uri_arr[$j])) {
										$pos = strpos($uri_arr[$j], '_');
										if (isset($modules->gen[substr($uri_arr[$j], 0, $pos)]) &&
											substr($uri_arr[$j], $pos + 1, strlen($uri_arr[$j])) == $modules->gen[substr($uri_arr[$j], 0, $pos)]) {
											$is_page = true;
										}
									}
								}
							}
							if (isset($is_page) && $is_page) {
								$link['selected'] = $selected;
							}
						} elseif (empty($uri_arr[2]) && $modules->mod == $uri_arr[0] && $modules->page == $uri_arr[1] && empty($modules->id) && empty($modules->cat) && empty($modules->gen) && $modules->action == $modules->page) {
							$link['selected'] = $selected;
						}
						break;
					default:
						$link['href'] = $pages[$i]['uri'];
						$link['selected'] = '';
				}
				$link['target'] = ($pages[$i]['mode'] == '2' || $pages[$i]['mode'] == '3') && $pages[$i]['target'] == '2' ? ' onclick="window.open(this.href); return false"' : '';
				$link['title'] = $pages[$i]['title'];

				$navbar[$pages[$i]['block_name']][$i] = $link;
			}
		}
		return $navbar;
	}
	return false;
}
?>