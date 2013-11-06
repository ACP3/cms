<?php
function smarty_function_uri($params)
{
    $alias = isset($params['alias']) && \ACP3\Core\Validate::isNumber($params['alias']) === true ? $params['alias'] : 1;
    return \ACP3\Core\Registry::get('URI')->route(!empty($params['args']) ? $params['args'] : '', (int)$alias);
}
/* vim: set expandtab: */