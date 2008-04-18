<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$date = ' AND (start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')';

if (validate::isNumber($modules->id) && $db->select('id', 'pages', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == 1) {
	if (!cache::check('pages_list_id_' . $modules->id)) {
		cache::create('pages_list_id_' . $modules->id, $db->select('mode, uri, text', 'pages', 'id = \'' . $modules->id . '\''));
	}
	$page = cache::output('pages_list_id_' . $modules->id);

	if ($page[0]['mode'] == '1') {
		/**
		 * Erweitert die Breadcrumb Klasse, damit für die statischen Seiten die Brotkrümelspur erstellt werden kann
		 *
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
			 * Gibt die Brotkrümelspur bzw. den Seitentitel einer statischen Seite aus
			 *
			 * @param integer $mode
			 * 	1 = Brotkrümelspur ausgeben
			 * 	2 = Nur Seitentitel ausgeben
			 * @param integer $id
			 * 	ID der jeweiligen statischen Seite
			 * @return string
			 */
			public static function output($mode = 1, $id = 0)
			{
				global $db, $modules, $tpl;

				// Zuweisung der ID von der Elternseite bzw. der Ausgangsseite
				$id = !empty($id) ? $id : $modules->id;

				$page = $db->select('parent, title', 'pages', 'id = \'' . $id . '\' AND mode = \'1\'');
				// Brotkrümelspur ausgeben
				if ($mode == 1) {
					if (empty(self::$end)) {
						self::$end = $page[0]['title'];
					}
					if ($db->select('parent', 'pages', 'id = \'' . $page[0]['parent'] . '\' AND mode = \'1\'', 0, 0, 0, 1) > 0) {
						$parent = $db->select('title', 'pages', 'id = \'' . $page[0]['parent'] . '\' AND mode = \'1\'');
						self::assign($parent[0]['title'], uri('pages/list/id_' . $page[0]['parent']));

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
		}

		$tpl->assign('text', $db->escape($page[0]['text'], 3));
	} elseif ($page[0]['mode'] == '2') {
		redirect($page[0]['uri']);
	} else {
		redirect(0, $db->escape($page[0]['uri'], 3));
	}
} else {
	redirect('errors/404');
}
$content = $tpl->fetch('pages/list.html');
?>