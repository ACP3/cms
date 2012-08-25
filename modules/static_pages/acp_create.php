<?php
/**
 * Static Pages
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Modules::check('menu_items', 'acp_create') === true)
	require_once MODULES_DIR . 'menu_items/functions.php';

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = $lang->t('common', 'select_date');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = $lang->t('static_pages', 'title_to_short');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = $lang->t('static_pages', 'text_to_short');
	if (ACP3_Modules::check('menu_items', 'create') === true) {
		if ($_POST['create'] != 1 && $_POST['create'] != 0)
			$errors[] = $lang->t('static_page', 'select_create_menu_item');
		if ($_POST['create'] == 1) {
			if (ACP3_Validate::isNumber($_POST['block_id']) === false)
				$errors['block-id'] = $lang->t('menu_items', 'select_block');
			if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === false)
				$errors['parent'] = $lang->t('menu_items', 'select_superior_page');
			if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === true) {
				// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
				$parent_block = $db->select('block_id', 'menu_items', 'id = \'' . $_POST['parent'] . '\'');
				if (!empty($parent_block) && $parent_block[0]['block_id'] != $_POST['block_id'])
					$errors['parent'] = $lang->t('menu_items', 'superior_page_not_allowed');
			}
			if ($_POST['display'] != 0 && $_POST['display'] != 1)
				$errors[] = $lang->t('menu_items', 'select_item_visibility');
		}
	}
	if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => $_POST['start'],
			'end' => $_POST['end'],
			'title' => $db->escape($_POST['title']),
			'text' => $db->escape($_POST['text'], 2),
			'user_id' => $auth->getUserId(),
		);

		$db->link->beginTransaction();
		$bool = $db->insert('static_pages', $insert_values);
		$last_id = $db->link->lastInsertId();
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
			ACP3_SEO::insertUriAlias('static_pages/list/id_' . $last_id, $_POST['alias'], $db->escape($_POST['seo_keywords']), $db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);
		$db->link->commit();

		if ($_POST['create'] == 1 && ACP3_Modules::check('menu_items', 'create') === true) {
			$insert_values = array(
				'id' => '',
				'mode' => 4,
				'block_id' => $_POST['block_id'],
				'display' => $_POST['display'],
				'title' => $db->escape($_POST['title']),
				'uri' => 'static_pages/list/id_' . $last_id . '/',
				'target' => 1,
			);

			menuItemsInsertNode($_POST['parent'], $insert_values);
			setMenuItemsCache();
		}

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/static_pages');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	if (ACP3_Modules::check('menu_items', 'acp_create') === true) {
		$options = array();
		$options[0]['name'] = 'create';
		$options[0]['checked'] = selectEntry('create', '1', '0', 'checked');
		$options[0]['lang'] = $lang->t('static_pages', 'create_menu_item');
		$tpl->assign('options', $options);

		// Block
		$blocks = $db->select('id, title', 'menu_items_blocks');
		$c_blocks = count($blocks);
		for ($i = 0; $i < $c_blocks; ++$i) {
			$blocks[$i]['selected'] = selectEntry('block_id', $blocks[$i]['id']);
		}
		$tpl->assign('blocks', $blocks);

		$display = array();
		$display[0]['value'] = 1;
		$display[0]['selected'] = selectEntry('display', 1, 1, 'checked');
		$display[0]['lang'] = $lang->t('common', 'yes');
		$display[1]['value'] = 0;
		$display[1]['selected'] = selectEntry('display', '0', '', 'checked');
		$display[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('display', $display);

		$tpl->assign('pages_list', menuItemsList());
	}

	$tpl->assign('publication_period', $date->datepicker(array('start', 'end')));

	$defaults = array(
		'title' => '',
		'text' => '',
		'alias' => '',
		'seo_keywords' => '',
		'seo_description' => ''
	);

	$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields());

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('static_pages/acp_create.tpl'));
}
