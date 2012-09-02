<?php
function smarty_function_js_libraries($params)
{
	ACP3_CMS::$view->enableJsLibraries(explode(',', $params['enable']));
}
/* vim: set expandtab: */