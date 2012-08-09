<?php
function smarty_function_check_access($params, $template)
{
	if ($params['mode'] === 'bool') {
		$action = explode('|', $params['action']);
		return ACP3_Modules::check($action[0], $action[1]) === true ? true : false;
	} else {
		$action = array();
		if (isset($params['uri'])) {
			$query = explode('/', strtolower($params['uri']));
			if (isset($query[0]) && $query[0] === 'acp') {
				$action[0] = (isset($query[1]) ? $query[1] : 'acp');
				$action[1] = 'acp_' . (isset($query[2]) ? $query[2] : 'list');
			} else {
				$action[0] = $query[0];
				$action[1] = isset($query[1]) ? $query[1] : 'list';
			}
		} else {
			$action = explode('|', $params['action']);
		}

		if (ACP3_Modules::check($action[0], $action[1]) === true) {
			global $lang, $uri;

			$access_check = array();

			if (isset($params['icon'])) {
				$path = DESIGN_PATH . 'images/' . $params['icon'] . '.png';
				$access_check['icon'] = $path;
			}
			if (isset($params['title']))
				$access_check['title'] = $params['title'];
			if (isset($params['uri']))
				$access_check['uri'] = $uri->route($params['uri']);
			if (isset($params['lang'])) {
				$lang_ary = explode('|', $params['lang']);
				$access_check['lang'] = $lang->t($lang_ary[0], $lang_ary[1]);
			} else {
				$access_check['lang'] = $lang->t($action[0], $action[1]);
			}

			// Dimensionen der Grafik bestimmen
			if ($params['mode'] === 'link' && isset($params['icon'])) {
				$access_check['width'] = $access_check['height'] = '';

				if (!empty($params['width']) && !empty($params['height']) &&
					ACP3_Validate::isNumber($params['width']) === true && ACP3_Validate::isNumber($params['height']) === true) {
					$access_check['width'] = ' width="' . $params['width'] . '"';
					$access_check['height'] = ' height="' . $params['height'] . '"';
				} elseif (is_file(ACP3_ROOT . $path) === true) {
					$picInfos = getimagesize(ACP3_ROOT . $path);
					$access_check['width'] = ' width="' . $picInfos[0] . '"';
					$access_check['height'] = ' height="' . $picInfos[1] . '"';
				}
			}

			$access_check['mode'] = $params['mode'];
			$template->smarty->assign('access_check', $access_check);
			return $template->smarty->fetch('common/access_check.tpl');
		} elseif ($params['mode'] == 'link' && isset($params['title'])) {
			return $params['title'];
		}
	}
	return '';
}
/* vim: set expandtab: */