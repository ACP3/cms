<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/**
 * @param $params
 * @return mixed
 */
function smarty_function_wysiwyg($params)
{
    $params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];

    $wysiwyg = new \ACP3\Core\WYSIWYG();
    \ACP3\Core\WYSIWYG::factory(CONFIG_WYSIWYG, $params);
    return $wysiwyg->display();
}
/* vim: set expandtab: */