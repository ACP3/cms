<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (!validate::date($form['start'], $form['end']))
		$errors[] = $lang->t('common', 'select_date');
	if (strlen($form['name']) < 3)
		$errors[] = $lang->t('gallery', 'type_in_gallery_name');
	if (!validate::isUriSafe($form['alias']) || validate::UriAliasExists($form['alias']))
		$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($form['start']),
			'end' => $date->timestamp($form['end']),
			'name' => db::escape($form['name']),
		);

		$bool = $db->insert('gallery', $insert_values);
		$bool2 = $uri->insertUriAlias($form['alias'], 'gallery/pics/id_' . $db->link->lastInsertID());

		$content = comboBox($bool && $bool2 ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/gallery'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('publication_period', $date->datepicker(array('start', 'end')));

	$tpl->assign('form', isset($form) ? $form : array('name' => '', 'alias' => ''));

	$content = modules::fetchTemplate('gallery/create.html');
}
