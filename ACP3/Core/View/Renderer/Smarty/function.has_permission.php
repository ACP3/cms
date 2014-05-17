<?php
function smarty_function_has_permission($params)
{
    if (isset($params['path']) === true) {
        return \ACP3\Core\Modules::hasPermission($params['path']);
    } else {
        return false;
    }
}
/* vim: set expandtab: */