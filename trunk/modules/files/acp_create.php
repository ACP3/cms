<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'categories/functions.php';

$settings = ACP3_Config::getSettings('files');

if (isset($_POST['submit']) === true) {
	if (isset($_POST['external'])) {
		$file = $_POST['file_external'];
	} else {
		$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
		$file['name'] = $_FILES['file_internal']['name'];
		$file['size'] = $_FILES['file_internal']['size'];
	}

	if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_date');
	if (strlen($_POST['link_title']) < 3)
		$errors['link-title'] = ACP3_CMS::$lang->t('files', 'type_in_link_title');
	if (isset($_POST['external']) && (empty($file) || empty($_POST['filesize']) || empty($_POST['unit'])))
		$errors['external'] = ACP3_CMS::$lang->t('files', 'type_in_external_resource');
	if (!isset($_POST['external']) &&
		(empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK))
		$errors['file-internal'] = ACP3_CMS::$lang->t('files', 'select_internal_resource');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = ACP3_CMS::$lang->t('files', 'description_to_short');
	if (strlen($_POST['cat_create']) < 3 && categoriesCheck($_POST['cat']) === false)
		$errors['cat'] = ACP3_CMS::$lang->t('files', 'select_category');
	if (strlen($_POST['cat_create']) >= 3 && categoriesCheckDuplicate($_POST['cat_create'], 'files') === true)
		$errors['cat-create'] = ACP3_CMS::$lang->t('categories', 'category_already_exists');
	if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) && (ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3_CMS::$lang->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		if (is_array($file) === true) {
			$result = moveFile($file['tmp_name'], $file['name'], 'files');
			$new_file = $result['name'];
			$filesize = $result['size'];
		} else {
			$_POST['filesize'] = (float) $_POST['filesize'];
			$new_file = $file;
			$filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
		}

		$insert_values = array(
			'id' => '',
			'start' => $_POST['start'],
			'end' => $_POST['end'],
			'category_id' => strlen($_POST['cat_create']) >= 3 ? categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
			'file' => $new_file,
			'size' => $filesize,
			'link_title' => $_POST['link_title'],
			'text' => $_POST['text'],
			'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
			'user_id' => ACP3_CMS::$auth->getUserId(),
		);


		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'files', $insert_values);
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
			ACP3_SEO::insertUriAlias('files/details/id_' . ACP3_CMS::$db2->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/files');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Datumsauswahl
	ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end')));

	$units = array();
	$units[0]['value'] = 'Byte';
	$units[0]['selected'] = selectEntry('unit', 'Byte');
	$units[1]['value'] = 'KiB';
	$units[1]['selected'] = selectEntry('unit', 'KiB');
	$units[2]['value'] = 'MiB';
	$units[2]['selected'] = selectEntry('unit', 'MiB');
	$units[3]['value'] = 'GiB';
	$units[3]['selected'] = selectEntry('unit', 'GiB');
	$units[4]['value'] = 'TiB';
	$units[4]['selected'] = selectEntry('unit', 'TiB');
	ACP3_CMS::$view->assign('units', $units);

	// Formularelemente
	ACP3_CMS::$view->assign('categories', categoriesList('files', '', true));

	if (ACP3_Modules::check('comments', 'functions') === true && $settings['comments'] == 1) {
		$options = array();
		$options[0]['name'] = 'comments';
		$options[0]['checked'] = selectEntry('comments', '1', '0', 'checked');
		$options[0]['lang'] = ACP3_CMS::$lang->t('system', 'allow_comments');
		ACP3_CMS::$view->assign('options', $options);
	}

	ACP3_CMS::$view->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');

	$defaults = array(
		'link_title' => '',
		'file_internal' => '',
		'file_external' => '',
		'filesize' => '',
		'text' => '',
		'alias' => '',
		'seo_keywords' => '',
		'seo_description' => '',
	);

	ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields());

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('files/acp_create.tpl'));
}