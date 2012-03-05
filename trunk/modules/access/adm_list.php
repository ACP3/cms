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

getRedirectMessage();

$roles = ACP3_ACL::getAllRoles();
$c_roles = count($roles);

if ($c_roles > 0) {
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
	}
	$tpl->assign('roles', $roles);
}
ACP3_View::setContent(ACP3_View::fetchTemplate('access/adm_list.tpl'));
