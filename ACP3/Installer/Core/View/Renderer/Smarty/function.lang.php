<?php
function smarty_function_lang($params)
{
    return \ACP3\Core\Registry::get('Lang')->t($params['t']);
}
/* vim: set expandtab: */