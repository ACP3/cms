<?php
function smarty_function_mark($params)
{
	ACP3_CMS::$view->assign('checkbox_name', $params['name']);
	ACP3_CMS::$view->assign('mark_all_id', !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all');
	return ACP3_CMS::$view->fetchTemplate('system/mark.tpl');
}
/* vim: set expandtab: */