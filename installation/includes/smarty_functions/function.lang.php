<?php
function smarty_function_lang($params)
{
	global $lang;

	return $lang->t($params['t']);
}
/* vim: set expandtab: */