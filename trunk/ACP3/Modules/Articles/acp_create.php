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

$access_to_menus = ACP3\Core\Modules::check('menus', 'acp_create_item');
if ($access_to_menus === true)
	require_once MODULES_DIR . 'menus/functions.php';

if (isset($_POST['submit']) === true) {
	if (ACP3\Core\Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'select_date');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3\CMS::$injector['Lang']->t('articles', 'title_to_short');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = ACP3\CMS::$injector['Lang']->t('articles', 'text_to_short');
	if ($access_to_menus === true && isset($_POST['create']) === true) {
		if ($_POST['create'] == 1) {
			if (ACP3\Core\Validate::isNumber($_POST['block_id']) === false)
				$errors['block-id'] = ACP3\CMS::$injector['Lang']->t('menus', 'select_menu_bar');
			if (!empty($_POST['parent']) && ACP3\Core\Validate::isNumber($_POST['parent']) === false)
				$errors['parent'] = ACP3\CMS::$injector['Lang']->t('menus', 'select_superior_page');
			if (!empty($_POST['parent']) && ACP3\Core\Validate::isNumber($_POST['parent']) === true) {
				// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
				$parent_block = ACP3\CMS::$injector['Db']->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($_POST['parent']));
				if (!empty($parent_block) && $parent_block != $_POST['block_id'])
					$errors['parent'] = ACP3\CMS::$injector['Lang']->t('menus', 'superior_page_not_allowed');
			}
			if ($_POST['display'] != 0 && $_POST['display'] != 1)
				$errors[] = ACP3\CMS::$injector['Lang']->t('menus', 'select_item_visibility');
		}
	}
	if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3\Core\Validate::isUriSafe($_POST['alias']) === false || ACP3\Core\Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3\CMS::$injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => ACP3\CMS::$injector['Date']->toSQL($_POST['start']),
			'end' => ACP3\CMS::$injector['Date']->toSQL($_POST['end']),
			'title' => ACP3\Core\Functions::str_encode($_POST['title']),
			'text' => ACP3\Core\Functions::str_encode($_POST['text'], true),
			'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
		);

		ACP3\CMS::$injector['Db']->beginTransaction();
		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'articles', $insert_values);
		$last_id = ACP3\CMS::$injector['Db']->lastInsertId();
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
			ACP3\Core\SEO::insertUriAlias('articles/details/id_' . $last_id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
		ACP3\CMS::$injector['Db']->commit();

		if (isset($_POST['create']) === true && $access_to_menus === true) {
			$insert_values = array(
				'id' => '',
				'mode' => 4,
				'block_id' => $_POST['block_id'],
				'parent_id' => (int) $_POST['parent'],
				'display' => $_POST['display'],
				'title' => ACP3\Core\Functions::str_encode($_POST['title']),
				'uri' => 'articles/details/id_' . $last_id . '/',
				'target' => 1,
			);

			$nestedSet = new ACP3\Core\NestedSet('menu_items', true);
			$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
			setMenuItemsCache();
		}

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/articles');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	if ($access_to_menus === true) {
		$lang_options = array(ACP3\CMS::$injector['Lang']->t('articles', 'create_menu_item'));
		ACP3\CMS::$injector['View']->assign('options', ACP3\Core\Functions::selectGenerator('create', array(1), $lang_options, 0, 'checked'));

		// Block
		ACP3\CMS::$injector['View']->assign('blocks', menusDropdown());

		$lang_display = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
		ACP3\CMS::$injector['View']->assign('display', ACP3\Core\Functions::selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

		ACP3\CMS::$injector['View']->assign('pages_list', menuItemsList());
	}

	ACP3\CMS::$injector['View']->assign('publication_period', ACP3\CMS::$injector['Date']->datepicker(array('start', 'end')));

	$defaults = array(
		'title' => '',
		'text' => '',
		'alias' => '',
		'seo_keywords' => '',
		'seo_description' => ''
	);

	ACP3\CMS::$injector['View']->assign('SEO_FORM_FIELDS', ACP3\Core\SEO::formFields());

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
