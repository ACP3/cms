<?php
function smarty_function_load_module($params)
{
    if (\ACP3\Core\Modules::hasPermission($params['module']) === true) {
        $pathArray = array_map(function($value) {
            return str_replace(' ', '', ucfirst(str_replace('_', ' ', $value)));
        }, explode('/', $params['module']));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        if ($pathArray[0] !== 'Frontend') {
            $className = "\\ACP3\\Modules\\" . $pathArray[1] . "\\Controller\\" . $pathArray[0] . "\\"  .$pathArray[2];
        } else {
            $className = "\\ACP3\\Modules\\" . $pathArray[1] . "\\Controller\\"  .$pathArray[2];
        }

        $action = 'action' . $pathArray[3];
        /** @var \ACP3\Core\Modules\Controller $controller */
        $controller = new $className(
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
        $controller->$action();
        $controller->display();
    }
}
/* vim: set expandtab: */