<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (validate::date($form['start'], $form['end']) === false)
		$errors[] = $lang->t('common', 'select_date');
	if (strlen($form['name']) < 3)
		$errors['name'] = $lang->t('gallery', 'type_in_gallery_name');
	if (CONFIG_SEO_ALIASES === true && !empty($form['alias']) && (validate::isUriSafe($form['alias']) === false || validate::uriAliasExists($form['alias']) === true))
		$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($form['start']),
			'end' => $date->timestamp($form['end']),
			'name' => $db->escape($form['name']),
			'user_id' => $auth->getUserId(),
		);

		$bool = $db->insert('gallery', $insert_values);
		if (CONFIG_SEO_ALIASES === true && !empty($form['alias']))
			seo::insertUriAlias($form['alias'], 'gallery/pics/id_' . $db->link->lastInsertID(), $db->escape($form['seo_keywords']), $db->escape($form['seo_description']));

		$session->unsetFormToken();

		setRedirectMessage($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/gallery');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	// Datumsauswahl
	$tpl->assign('publication_period', $date->datepicker(array('start', 'end')));

	$tpl->assign('form', isset($form) ? $form : array('name' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('gallery/create.tpl'));
}
