<?php
/**
 ** Access Control List
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'acl_roles', 'id = ' . ACP3_CMS::$uri->id) == 1) {
	$nestedSet = new ACP3_NestedSet('acl_roles');
	$nestedSet->order(ACP3_CMS::$uri->id, ACP3_CMS::$uri->action);

	ACP3_Cache::purge(0, 'acl');

	ACP3_CMS::$uri->redirect('acp/permissions');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
