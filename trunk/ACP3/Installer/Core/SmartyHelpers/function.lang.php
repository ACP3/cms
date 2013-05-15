<?php
function smarty_function_lang($params)
{
	return \ACP3\Installer\Installer::$injector['Lang']->t($params['t']);
}
/* vim: set expandtab: */