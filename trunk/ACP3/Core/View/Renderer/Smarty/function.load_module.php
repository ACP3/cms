<?php
/**
 * @param $params
 * @throws Exception
 */
function smarty_function_load_module($params)
{
    $pathArray = array_map(function ($value) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $value)));
    }, explode('/', $params['module']));

    if (empty($pathArray[2]) === true) {
        $pathArray[2] = 'Index';
    }
    if (empty($pathArray[3]) === true) {
        $pathArray[3] = 'Index';
    }

    if ($pathArray[0] !== 'Frontend') {
        $className = "\\ACP3\\Modules\\" . $pathArray[1] . "\\Controller\\" . $pathArray[0] . "\\" . $pathArray[2];
    } else {
        $className = "\\ACP3\\Modules\\" . $pathArray[1] . "\\Controller\\" . $pathArray[2];
    }

    if (class_exists($className)) {
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

        if (method_exists($controller, $action) === true) {
            $controller->preDispatch();
            $controller->$action();
            $controller->display();
        } else {
            throw new Exception('Controller action ' . $className . '::' . $action . '() was not found!');
        }
    } else {
        throw new Exception('Class ' . $className . '() was not found!');
    }
}
/* vim: set expandtab: */