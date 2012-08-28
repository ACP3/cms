<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Installer
 */

if (defined('IN_INSTALL') === false)
	exit;

if ($uri->action === 'do') {
	$results = array();
	// Zuerst die wichtigen System-Module aktualisieren...
	$update_first = array('system', 'permissions', 'users');
	foreach ($update_first as $row) {
		$result = updateModule($row);
		$results[] = array(
			'text' => sprintf($lang->t('installation', 'db_update_text'), $lang->t($row, $row)),
			'class' => $result === 1 ? 'success' : ($result === 0 ? 'important' : 'info'),
			'result_text' => $lang->t('installation', $result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
		);
	}

	// ...danach die Restlichen
	$modules = scandir(MODULES_DIR);
	foreach ($modules as $row) {
		if ($row !== '.' && $row !== '..' &&
			ACP3_Modules::isInstalled($row) && in_array($row, $update_first) === false) {
			$result = updateModule($row);
			$text = $lang->t('installation', $result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'));
			$results[] = array(
				'text' => sprintf($lang->t('installation', 'db_update_text'), $lang->t($row, $row)),
				'class' => $result === 1 ? 'success' : ($result === 0 ? 'important' : 'info'),
				'result_text' => $lang->t('installation', $result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
			);
		}
	}
	$tpl->assign('results', $results);

	// Cache leeren
	ACP3_Cache::purge('minify');
	ACP3_Cache::purge('sql');
	ACP3_Cache::purge('tpl_compiled');
}

$content = $tpl->fetch('db_update.tpl');