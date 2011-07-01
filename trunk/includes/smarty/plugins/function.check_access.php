<?php
function smarty_function_check_access($params, $template)
{
	$action = explode('|', $params['action']);
	if ($params['mode'] == 'bool') {
		return modules::check($action[0], $action[1]) == 1 ? true : false;
	} elseif (modules::check($action[0], $action[1]) == 1) {
		global $lang;

		$access_check = array();

		if (isset($params['icon'])) {
			$path = 'images/crystal/' . $params['icon'] . '.png';
			$access_check['icon'] = ROOT_DIR . $path;
		}
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

		// Dimensionen der Grafik bestimmen
		if ($params['mode'] == 'link' && isset($params['icon'])) {
			$access_check['width'] = $access_check['height'] = '';

			if (!empty($params['width']) && !empty($params['height']) &&
				validate::isNumber($params['width']) && validate::isNumber($params['height'])) {
				$access_check['width'] = ' width="' . $params['width'] . '"';
				$access_check['height'] = ' height="' . $params['height'] . '"';
			} elseif (is_file(ACP3_ROOT . $path)) {
				$picInfos = getimagesize(ACP3_ROOT . $path);
				$access_check['width'] = ' width="' . $picInfos[0] . '"';
				$access_check['height'] = ' height="' . $picInfos[1] . '"';
			}
		}

		$access_check['mode'] = $params['mode'];
		$template->assign('access_check', $access_check);
		return $template->fetch('common/access_check.html');
	} elseif ($params['mode'] == 'link' && isset($params['title'])) {
		return $params['title'];
	}
	return '';
}
/* vim: set expandtab: */
?>