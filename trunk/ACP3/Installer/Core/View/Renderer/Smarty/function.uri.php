<?php
function smarty_function_uri($params)
{
    return \ACP3\Core\Registry::get('URI')->route(!empty($params['args']) ? $params['args'] : '');
}
/* vim: set expandtab: */