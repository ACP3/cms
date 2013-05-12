<?php
function smarty_function_js_libraries($params)
{
	\ACP3\CMS::$injector['View']->enableJsLibraries(explode(',', $params['enable']));
}
/* vim: set expandtab: */