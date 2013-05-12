<?php
function smarty_function_mark($params)
{
	\ACP3\CMS::$injector['View']->assign('checkbox_name', $params['name']);
	\ACP3\CMS::$injector['View']->assign('mark_all_id', !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all');
	return \ACP3\CMS::$injector['View']->fetchTemplate('system/mark.tpl');
}
/* vim: set expandtab: */