<?php
function smarty_function_load_module($params)
{
    $module = explode('|', $params['module']);

    if (\ACP3\Core\Modules::hasPermission($module[0], $module[1]) === true) {
        $className = "\\ACP3\\Modules\\" . ucfirst($module[0]) . "\\Controller\\Index";
        $action = 'action' . str_replace(' ', '', ucfirst(str_replace('_', ' ', $module[1])));
        $mod = new $className(
            \ACP3\Core\Registry::get('Auth'),
            \ACP3\Core\Registry::get('Breadcrumb'),
            \ACP3\Core\Registry::get('Date'),
            \ACP3\Core\Registry::get('Db'),
            \ACP3\Core\Registry::get('Lang'),
            \ACP3\Core\Registry::get('Session'),
            \ACP3\Core\Registry::get('URI'),
            \ACP3\Core\Registry::get('View'),
            \ACP3\Core\Registry::get('SEO')
        );
        $mod->$action();
    }
}
/* vim: set expandtab: */