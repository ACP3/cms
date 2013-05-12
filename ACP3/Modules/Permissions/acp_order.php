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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	$nestedSet = new ACP3\Core\NestedSet('acl_roles');
	$nestedSet->order(ACP3\CMS::$injector['URI']->id, ACP3\CMS::$injector['URI']->action);

	ACP3\Core\Cache::purge(0, 'acl');

	ACP3\CMS::$injector['URI']->redirect('acp/permissions');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
