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
		$tpl->assign('text', $db->escape($page[0]['text'], 3));
		$content = $tpl->fetch('pages/list.html');
	} elseif ($page[0]['mode'] == '2') {
		$params = explode('/', $db->escape($page[0]['uri'], 3));
		$c_params = count($params);

		if (!empty($params[2])) {
			for ($i = 2; $i < $c_params; ++$i) {
				if (preg_match('/^(([a-z0-9-]+)_(.+))$/', $params[$i])) {
					$param = explode('_', $params[$i], 2);
					$modules->$param[0] = $param[1];
				}
			}
		}
		if (!empty($params[0]) && !empty($params[1]) && $modules->check($params[0], $params[1])) {
			include ACP3_ROOT . 'modules/' . $params[0] . '/' . $params[1] . '.php';
		} else {
			redirect('errors/404');
		}
	} else {
		redirect(0, $db->escape($page[0]['uri'], 3));
	}
} else {
	redirect('errors/404');
}
?>