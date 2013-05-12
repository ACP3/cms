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

$settings = ACP3\Core\Config::getSettings('files');

if (isset($_POST['submit']) === true) {
	if (isset($_POST['external'])) {
		$file = $_POST['file_external'];
	} else {
		$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
		$file['name'] = $_FILES['file_internal']['name'];
		$file['size'] = $_FILES['file_internal']['size'];
	}

	if (ACP3\Core\Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'select_date');
	if (strlen($_POST['title']) < 3)
		$errors['link-title'] = ACP3\CMS::$injector['Lang']->t('files', 'type_in_title');
	if (isset($_POST['external']) && (empty($file) || empty($_POST['filesize']) || empty($_POST['unit'])))
		$errors['external'] = ACP3\CMS::$injector['Lang']->t('files', 'type_in_external_resource');
	if (!isset($_POST['external']) &&
		(empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK))
		$errors['file-internal'] = ACP3\CMS::$injector['Lang']->t('files', 'select_internal_resource');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = ACP3\CMS::$injector['Lang']->t('files', 'description_to_short');
	if (strlen($_POST['cat_create']) < 3 && categoriesCheck($_POST['cat']) === false)
		$errors['cat'] = ACP3\CMS::$injector['Lang']->t('files', 'select_category');
	if (strlen($_POST['cat_create']) >= 3 && categoriesCheckDuplicate($_POST['cat_create'], 'files') === true)
		$errors['cat-create'] = ACP3\CMS::$injector['Lang']->t('categories', 'category_already_exists');
	if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) && (ACP3\Core\Validate::isUriSafe($_POST['alias']) === false || ACP3\Core\Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3\CMS::$injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		if (is_array($file) === true) {
			$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'files');
			$new_file = $result['name'];
			$filesize = $result['size'];
		} else {
			$_POST['filesize'] = (float) $_POST['filesize'];
			$new_file = $file;
			$filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
		}

		$insert_values = array(
			'id' => '',
			'start' => ACP3\CMS::$injector['Date']->toSQL($_POST['start']),
			'end' => ACP3\CMS::$injector['Date']->toSQL($_POST['end']),
			'category_id' => strlen($_POST['cat_create']) >= 3 ? categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
			'file' => $new_file,
			'size' => $filesize,
			'title' => ACP3\Core\Functions::str_encode($_POST['title']),
			'text' => ACP3\Core\Functions::str_encode($_POST['text'], true),
			'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
			'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
		);


		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'files', $insert_values);
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
			ACP3\Core\SEO::insertUriAlias('files/details/id_' . ACP3\CMS::$injector['Db']->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/files');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Datumsauswahl
	ACP3\CMS::$injector['View']->assign('publication_period', ACP3\CMS::$injector['Date']->datepicker(array('start', 'end')));

	$units = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB');
	ACP3\CMS::$injector['View']->assign('units', ACP3\Core\Functions::selectGenerator('units', $units, $units, ''));

	// Formularelemente
	ACP3\CMS::$injector['View']->assign('categories', categoriesList('files', '', true));

	if (ACP3\Core\Modules::check('comments', 'functions') === true && $settings['comments'] == 1) {
		$options = array();
		$options[0]['name'] = 'comments';
		$options[0]['checked'] = ACP3\Core\Functions::selectEntry('comments', '1', '0', 'checked');
		$options[0]['lang'] = ACP3\CMS::$injector['Lang']->t('system', 'allow_comments');
		ACP3\CMS::$injector['View']->assign('options', $options);
	}

	ACP3\CMS::$injector['View']->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');

	$defaults = array(
		'title' => '',
		'file_internal' => '',
		'file_external' => '',
		'filesize' => '',
		'text' => '',
		'alias' => '',
		'seo_keywords' => '',
		'seo_description' => '',
	);

	ACP3\CMS::$injector['View']->assign('SEO_FORM_FIELDS', ACP3\Core\SEO::formFields());

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3\CMS::$injector['Session']->generateFormToken();
}