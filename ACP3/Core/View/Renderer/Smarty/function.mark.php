<?php
function smarty_function_mark($params)
{
    \ACP3\Core\Registry::get('View')->assign('checkbox_name', $params['name']);
    \ACP3\Core\Registry::get('View')->assign('mark_all_id', !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all');
    return \ACP3\Core\Registry::get('View')->fetchTemplate('system/mark.tpl');
}
/* vim: set expandtab: */