<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$access = $db->select('id, name, modules', 'access', 0, 'name ASC', POS, CONFIG_ENTRIES);
$c_access = count($access);

if ($c_access > 0) {
	$tpl->assign('pagination', pagination($db->select('id', 'access', 0, 0, 0, 0, 1)));

	// Alle zur Zeit aktiven Module holen
	$mod_list = modules::modulesList();

	for ($i = 0; $i < $c_access; ++$i) {
		// Modulnamen anzeigen
		$access_to_mods = explode(',', $access[$i]['modules']);
		$c_access_to_mods = count($access_to_mods);
		$access[$i]['access_to_mod'] = '';

		foreach ($mod_list as $name => $info) {
			for ($j = 0; $j < $c_access_to_mods; ++$j) {
				$mod_name = substr($access_to_mods[$j], 0, -2);
				if ($info['active'] && $info['dir'] == $mod_name && substr($access_to_mods[$j], -1, 1) != '0') {
					$access[$i]['access_to_mod'].= $name . ', ';
				}
			}
		}
		$access[$i]['access_to_mod'] = substr($access[$i]['access_to_mod'], 0, -2);
	}
	$tpl->assign('access', $access);
}
$content = $tpl->fetch('access/adm_list.html');
?>