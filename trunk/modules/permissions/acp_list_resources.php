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

getRedirectMessage();

$resources = ACP3_CMS::$db2->fetchAll('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.privilege_id, p.key AS privilege_name FROM ' . DB_PRE . 'acl_resources AS r JOIN ' . DB_PRE . 'modules AS m ON(r.module_id = m.id) JOIN ' . DB_PRE . 'acl_privileges AS p ON(r.privilege_id = p.id) ORDER BY r.module_id ASC, r.page ASC');
$c_resources = count($resources);
$output = array();
for ($i = 0; $i < $c_resources; ++$i) {
	if (ACP3_Modules::isActive($resources[$i]['module_name']) === true) {
		$module = ACP3_CMS::$lang->t($resources[$i]['module_name'], $resources[$i]['module_name']);
		$output[$module][] = $resources[$i];
	}
}
ksort($output);
ACP3_CMS::$view->assign('resources', $output);
ACP3_CMS::$view->assign('can_delete_resource', ACP3_Modules::check('permissions', 'acp_delete_resources'));

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('permissions/acp_list_resources.tpl'));