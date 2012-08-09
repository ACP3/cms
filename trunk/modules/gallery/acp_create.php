<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = $lang->t('common', 'select_date');
	if (strlen($_POST['name']) < 3)
		$errors['name'] = $lang->t('gallery', 'type_in_gallery_name');
	if (CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($_POST['start']),
			'end' => $date->timestamp($_POST['end']),
			'name' => $db->escape($_POST['name']),
			'user_id' => $auth->getUserId(),
		);

		$bool = $db->insert('gallery', $insert_values);
		if (CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
			ACP3_SEO::insertUriAlias('gallery/pics/id_' . $db->link->lastInsertID(), $_POST['alias'], $db->escape($_POST['seo_keywords']), $db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);

		$session->unsetFormToken();

		setRedirectMessage($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/gallery');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Datumsauswahl
	$tpl->assign('publication_period', $date->datepicker(array('start', 'end')));

	$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields());

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('name' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('gallery/acp_create.tpl'));
}
