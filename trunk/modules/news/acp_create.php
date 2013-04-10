<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'categories/functions.php';

$settings = ACP3_Config::getSettings('news');

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_date');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3_CMS::$lang->t('news', 'title_to_short');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = ACP3_CMS::$lang->t('news', 'text_to_short');
	if (strlen($_POST['cat_create']) < 3 && categoriesCheck($_POST['cat']) === false)
		$errors['cat'] = ACP3_CMS::$lang->t('news', 'select_category');
	if (strlen($_POST['cat_create']) >= 3 && categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
		$errors['cat-create'] = ACP3_CMS::$lang->t('categories', 'category_already_exists');
	if (!empty($_POST['link_title']) && (empty($_POST['uri']) || ACP3_Validate::isNumber($_POST['target']) === false))
		$errors[] = ACP3_CMS::$lang->t('news', 'complete_hyperlink_statements');
	if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3_CMS::$lang->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => ACP3_CMS::$date->toSQL($_POST['start']),
			'end' => ACP3_CMS::$date->toSQL($_POST['end']),
			'title' => str_encode($_POST['title']),
			'text' => str_encode($_POST['text'], true),
			'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
			'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
			'category_id' => strlen($_POST['cat_create']) >= 3 ? categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
			'uri' => str_encode($_POST['uri'], true),
			'target' => (int) $_POST['target'],
			'link_title' => str_encode($_POST['link_title']),
			'user_id' => ACP3_CMS::$auth->getUserId(),
		);

		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'news', $insert_values);
		if ((bool) CONFIG_SEO_ALIASES === true)
			ACP3_SEO::insertUriAlias('news/details/id_' . ACP3_CMS::$db2->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/news');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Datumsauswahl
	ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end')));

	// Kategorien
	ACP3_CMS::$view->assign('categories', categoriesList('news', '', true));

	// Weiterlesen & Kommentare
		if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true)) {
		$i = 0;
		$options = array();
		if ($settings['readmore'] == 1) {
			$options[$i]['name'] = 'readmore';
			$options[$i]['checked'] = selectEntry('readmore', '1', '0', 'checked');
			$options[$i]['lang'] = ACP3_CMS::$lang->t('news', 'activate_readmore');
			$i++;
		}
		if ($settings['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
			$options[$i]['name'] = 'comments';
			$options[$i]['checked'] = selectEntry('comments', '1', '0', 'checked');
			$options[$i]['lang'] = ACP3_CMS::$lang->t('system', 'allow_comments');
		}
		ACP3_CMS::$view->assign('options', $options);
	}

	// Linkziel
	$target = array();
	$target[0]['value'] = '1';
	$target[0]['selected'] = selectEntry('target', '1');
	$target[0]['lang'] = ACP3_CMS::$lang->t('system', 'window_self');
	$target[1]['value'] = '2';
	$target[1]['selected'] = selectEntry('target', '2');
	$target[1]['lang'] = ACP3_CMS::$lang->t('system', 'window_blank');
	ACP3_CMS::$view->assign('target', $target);

	ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields());

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => '', 'uri' => '', 'link_title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

	ACP3_CMS::$session->generateFormToken();
}