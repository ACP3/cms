<?php
/**
 * @param $params
 */
function smarty_function_js_libraries($params)
{
    \ACP3\Core\Registry::get('View')->enableJsLibraries(explode(',', $params['enable']));
}
/* vim: set expandtab: */