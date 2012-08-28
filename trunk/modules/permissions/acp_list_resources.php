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

$resources = $db->query('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.privilege_id, p.key AS privilege_name FROM {pre}acl_resources AS r JOIN {pre}modules AS m ON(r.module_id = m.id) JOIN {pre}acl_privileges AS p ON(r.privilege_id = p.id) ORDER BY r.module_id ASC, r.page ASC');
$c_resources = count($resources);
$output = array();
for ($i = 0; $i < $c_resources; ++$i) {
	if (ACP3_Modules::isActive($resources[$i]['module_name']) === true) {
		$module = $lang->t($resources[$i]['module_name'], $resources[$i]['module_name']);
		$output[$module][] = $resources[$i];
	}
}
ksort($output);
$tpl->assign('resources', $output);
$tpl->assign('can_delete_resource', ACP3_Modules::check('permissions', 'acp_delete_resources'));

ACP3_View::setContent(ACP3_View::fetchTemplate('permissions/acp_list_resources.tpl'));