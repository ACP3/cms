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

require_once MODULES_DIR . 'access/functions.php';

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (empty($form['name']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (!empty($form['name']) && $db->countRows('*', 'access', 'name = \'' . $db->escape($form['name']) . '\'') == '1')
		$errors[] = $lang->t('access', 'access_level_already_exists');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$insert_values = array(
			'id' => '',
			'name' => $db->escape($form['name']),
			'modules' => buildAccessLevel($form['modules']),
		);

		$bool = $db->insert('access', $insert_values);

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/access'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('name' => ''));

	$mod_list = modules::modulesList();

	foreach ($mod_list as $name => $info) {
		if ($info['dir'] == 'errors' || !$info['active']) {
			unset($mod_list[$name]);
		} else {
			$dir = $info['dir'];
			$mod_list[$name]['read_checked'] = isset($form['modules'][$dir]['read']) ? ' checked="checked"' : '';
			$mod_list[$name]['create_checked'] = isset($form['modules'][$dir]['create']) ? ' checked="checked"' : '';
			$mod_list[$name]['edit_checked'] = isset($form['modules'][$dir]['edit']) ? ' checked="checked"' : '';
			$mod_list[$name]['delete_checked'] = isset($form['modules'][$dir]['delete']) ? ' checked="checked"' : '';
			$mod_list[$name]['full_checked'] = isset($form['modules'][$dir]['full']) ? ' checked="checked"' : '';
		}
	}
	$tpl->assign('mod_list', $mod_list);

	$content = modules::fetchTemplate('access/create.html');
}
