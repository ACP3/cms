<?php
/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$access_to_menus = ACP3_Modules::check('menus', 'acp_create_item');
if ($access_to_menus === true)
	require_once MODULES_DIR . 'menus/functions.php';

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_date');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3_CMS::$lang->t('articles', 'title_to_short');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = ACP3_CMS::$lang->t('articles', 'text_to_short');
	if ($access_to_menus === true) {
		if ($_POST['create'] != 1 && $_POST['create'] != 0)
			$errors[] = ACP3_CMS::$lang->t('static_page', 'select_create_menu_item');
		if ($_POST['create'] == 1) {
			if (ACP3_Validate::isNumber($_POST['block_id']) === false)
				$errors['block-id'] = ACP3_CMS::$lang->t('menus', 'select_menu_bar');
			if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === false)
				$errors['parent'] = ACP3_CMS::$lang->t('menus', 'select_superior_page');
			if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === true) {
				// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
				$parent_block = ACP3_CMS::$db2->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($_POST['parent']));
				if (!empty($parent_block) && $parent_block != $_POST['block_id'])
					$errors['parent'] = ACP3_CMS::$lang->t('menus', 'superior_page_not_allowed');
			}
			if ($_POST['display'] != 0 && $_POST['display'] != 1)
				$errors[] = ACP3_CMS::$lang->t('menus', 'select_item_visibility');
		}
	}
	if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3_CMS::$lang->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => ACP3_CMS::$date->toSQL($_POST['start']),
			'end' => ACP3_CMS::$date->toSQL($_POST['end']),
			'title' => $_POST['title'],
			'text' => $_POST['text'],
			'user_id' => ACP3_CMS::$auth->getUserId(),
		);

		ACP3_CMS::$db2->beginTransaction();
		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'articles', $insert_values);
		$last_id = ACP3_CMS::$db2->lastInsertId();
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
			ACP3_SEO::insertUriAlias('articles/list/id_' . $last_id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
		ACP3_CMS::$db2->commit();

		if ($_POST['create'] == 1 && $access_to_menus === true) {
			$insert_values = array(
				'id' => '',
				'mode' => 4,
				'block_id' => $_POST['block_id'],
				'parent_id' => (int) $_POST['parent'],
				'display' => $_POST['display'],
				'title' => $_POST['title'],
				'uri' => 'articles/list/id_' . $last_id . '/',
				'target' => 1,
			);

			$nestedSet = new ACP3_NestedSet('menu_items', true);
			$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
			setMenuItemsCache();
		}

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/articles');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	if ($access_to_menus === true) {
		$options = array();
		$options[0]['name'] = 'create';
		$options[0]['checked'] = selectEntry('create', '1', '0', 'checked');
		$options[0]['lang'] = ACP3_CMS::$lang->t('articles', 'create_menu_item');
		ACP3_CMS::$view->assign('options', $options);

		// Block
		ACP3_CMS::$view->assign('blocks', menusDropdown());

		$display = array();
		$display[0]['value'] = 1;
		$display[0]['selected'] = selectEntry('display', 1, 1, 'checked');
		$display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$display[1]['value'] = 0;
		$display[1]['selected'] = selectEntry('display', '0', '', 'checked');
		$display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('display', $display);

		ACP3_CMS::$view->assign('pages_list', menuItemsList());
	}

	ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end')));

	$defaults = array(
		'title' => '',
		'text' => '',
		'alias' => '',
		'seo_keywords' => '',
		'seo_description' => ''
	);

	ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields());

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('articles/acp_create.tpl'));
}
