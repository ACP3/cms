<?php
function smarty_function_js_libraries($params)
{
	ACP3_View::enableJsLibraries(explode(',', $params['enable']));
}
/* vim: set expandtab: */