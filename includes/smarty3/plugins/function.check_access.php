<?php
function smarty_function_check_access($params, &$smarty)
{
	$action = explode('|', $params['action']);
	if ($params['mode'] == 'bool') {
		return modules::check($action[0], $action[1]) == 1 ? true : false;
	} elseif (modules::check($action[0], $action[1]) == 1) {
		global $lang;

		$access_check = array();
		if (isset($params['icon']))
			$access_check['icon'] = ROOT_DIR . 'images/crystal/' . $params['icon'] . '.png';
		if (isset($params['title']))
			$access_check['title'] = $params['title'];
		if (isset($params['uri']))
			$access_check['uri'] = uri($params['uri']);
		if (isset($params['lang'])) {
			$lang_ary = explode('|', $params['lang']);
			$access_check['lang'] = $lang->t($lang_ary[0], $lang_ary[1]);
		} else {
			$access_check['lang'] = $lang->t($action[0], $action[1]);
		}
		$access_check['mode'] = $params['mode'];
		$smarty->assign('access_check', $access_check);
		return $smarty->fetch('common/access_check.html');
	} elseif ($params['mode'] == 'link' && isset($params['title'])) {
		return $params['title'];
	}
	return '';
}
/* vim: set expandtab: */
?>