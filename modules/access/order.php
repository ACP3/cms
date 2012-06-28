<?php
/**
 ** Access Control List
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'acl_roles', 'id = ' . $uri->id) == 1) {
	$nestedSet = new ACP3_NestedSet('acl_roles');
	$nestedSet->order($uri->id, $uri->action);

	ACP3_Cache::purge(0, 'acl');

	$uri->redirect('acp/access');
} else {
	$uri->redirect('errors/404');
}
