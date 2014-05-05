<?php
/**
 * @param $params
 * @return mixed
 */
function smarty_function_mark($params)
{
    static $init = false;

    $view = \ACP3\Core\Registry::get('View');
    $view->assign('checkbox_name', $params['name']);
    $view->assign('mark_all_id', !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all');
    $view->assign('is_initialized', $init);

    $init = true;

    return $view->fetchTemplate('system/mark.tpl');
}
/* vim: set expandtab: */