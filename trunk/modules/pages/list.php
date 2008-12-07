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

$time = $date->timestamp();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (validate::isNumber($uri->item) && $db->select('id', 'pages', 'id = \'' . $uri->item . '\'' . $period, 0, 0, 0, 1) == 1) {
	$page = getPagesCache($uri->item);

	// Statische Seite
	if ($page[0]['mode'] == '1') {
		$tpl->assign('text', $db->escape($page[0]['text'], 3));
		$content = $tpl->fetch('pages/list.html');
	// Dynamische Seite (ACP3 intern)
	} elseif ($page[0]['mode'] == '2') {
		$params = explode('/', $db->escape($page[0]['uri'], 3));
		$c_params = count($params);

		if (!empty($params[2])) {
			for ($i = 2; $i < $c_params; ++$i) {
				if (preg_match('/^(([a-z0-9-]+)_(.+))$/', $params[$i])) {
					$param = explode('_', $params[$i], 2);
					$uri->$param[0] = $param[1];
				}
			}
		}
		// Moduldatei laden...
		if (!empty($params[0]) && !empty($params[1]) && modules::check($params[0], $params[1])) {
			include ACP3_ROOT . 'modules/' . $params[0] . '/' . $params[1] . '.php';
		// ...ansonsten zur Fehlerseite weiterleiten
		} else {
			redirect('errors/404');
		}
	// Zu externer Seite weiterleiten
	} else {
		redirect(0, $db->escape($page[0]['uri'], 3));
	}
} else {
	redirect('errors/404');
}
?>