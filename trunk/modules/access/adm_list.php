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

$roles = acl::getAllRoles();
$c_roles = count($roles);

if ($c_roles > 0) {
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
	}
	$tpl->assign('roles', $roles);
}
$content = modules::fetchTemplate('access/adm_list.tpl');
