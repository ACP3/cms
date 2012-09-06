<?php
function smarty_function_mark($params)
{
	ACP3_CMS::$view->assign('checkbox_name', $params['name']);
	return ACP3_CMS::$view->fetchTemplate('system/mark.tpl');
}
/* vim: set expandtab: */