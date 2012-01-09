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

$resources = $db->query('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.privilege_id, p.key AS privilege_name FROM {pre}acl_resources AS r JOIN {pre}modules AS m ON(r.module_id = m.id) JOIN {pre}acl_privileges AS p ON(r.privilege_id = p.id) ORDER BY r.module_id ASC, r.page ASC');
$c_resources = count($resources);
$output = array();
for ($i = 0; $i < $c_resources; ++$i) {
	$module = $lang->t($resources[$i]['module_name'], $resources[$i]['module_name']);
	$output[$module][] = $resources[$i];
}
ksort($output);
$tpl->assign('resources', $output);

$content = modules::fetchTemplate('access/adm_list_resources.html');