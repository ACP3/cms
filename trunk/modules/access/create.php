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

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (empty($form['name']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (!empty($form['name']) && $db->select('COUNT(id)', 'access', 'name = \'' . $db->escape($form['name']) . '\'', 0, 0, 0, 1) == '1')
		$errors[] = $lang->t('access', 'access_level_already_exist');
	if (emptyCheck($form['modules']))
		$errors[] = $lang->t('access', 'select_modules');

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
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('name' => ''));

	$mod_list = modules::modulesList();

	foreach ($mod_list as $name => $info) {
		if ($info['dir'] == 'errors' || !$info['active']) {
			unset($mod_list[$name]);
		} else {
			for ($i = 0; $i < 3; ++$i) {
				$mod_list[$name]['options'][$i] = array(
					'value' => $i,
					'selected' => selectAccessLevel($info['dir'], (string) $i),
					'lang' => $lang->t('access', 'access_level_' . $i),
				);
			}
		}
	}
	$tpl->assign('mod_list', $mod_list);

	$content = $tpl->fetch('access/create.html');
}
?>