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

ACP3\Core\Functions::getRedirectMessage();

$roles = ACP3\Core\ACL::getAllRoles();
$c_roles = count($roles);

if ($c_roles > 0) {
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['spaces'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']);
	}
	ACP3\CMS::$injector['View']->assign('roles', $roles);
	ACP3\CMS::$injector['View']->assign('can_delete', ACP3\Core\Modules::check('permissions', 'acp_delete'));
	ACP3\CMS::$injector['View']->assign('can_order', ACP3\Core\Modules::check('permissions', 'acp_order'));	
}