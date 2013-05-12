<?php
/**
 * Menu Items
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'menus/functions.php';

if (isset($_POST['submit']) === true) {
	if (ACP3\Core\Validate::isNumber($_POST['mode']) === false)
		$errors['mode'] = ACP3\CMS::$injector['Lang']->t('menus', 'select_page_type');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3\CMS::$injector['Lang']->t('menus', 'title_to_short');
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
	if (ACP3\Core\Validate::isNumber($_POST['target']) === false ||
		$_POST['mode'] == 1 && (is_dir(MODULES_DIR . $_POST['module']) === false || preg_match('=/=', $_POST['module'])) ||
		$_POST['mode'] == 2 && ACP3\Core\Validate::isInternalURI($_POST['uri']) === false ||
		$_POST['mode'] == 3 && empty($_POST['uri']) ||
		$_POST['mode'] == 4 && (ACP3\Core\Validate::isNumber($_POST['articles']) === false || ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array($_POST['articles'])) == 0))
		$errors[] = ACP3\CMS::$injector['Lang']->t('menus', 'type_in_uri_and_target');
	if ($_POST['mode'] == 2 && (bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3\Core\Validate::isUriSafe($_POST['alias']) === false || ACP3\Core\Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3\CMS::$injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'mode' => ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match('/^(articles\/details\/id_([0-9]+)\/)$/', $_POST['uri']) ? '4' : $_POST['mode'],
			'block_id' => (int) $_POST['block_id'],
			'parent_id' => (int) $_POST['parent'],
			'display' => $_POST['display'],
			'title' => ACP3\Core\Functions::str_encode($_POST['title']),
			'uri' => $_POST['mode'] == 1 ? $_POST['module'] : ($_POST['mode'] == 4 ? 'articles/details/id_' . $_POST['articles'] . '/' : $_POST['uri']),
			'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
		);

		$nestedSet = new ACP3\Core\NestedSet('menu_items', true);
		$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);

		// Verhindern, dass externen URIs Aliase, Keywords, etc. zugewiesen bekommen
		if ($_POST['mode'] != 3) {
			$path = $_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'];
			if (ACP3\Core\SEO::uriAliasExists($_POST['uri'])) {
				$alias = !empty($_POST['alias']) ? $_POST['alias'] : ACP3\Core\SEO::getUriAlias($_POST['uri']);
				$keywords = ACP3\Core\SEO::getKeywords($_POST['uri']);
				$description = ACP3\Core\SEO::getDescription($_POST['uri']);
			} else {
				$alias = $_POST['alias'];
				$keywords = $_POST['seo_keywords'];
				$description = $_POST['seo_description'];
			}
			ACP3\Core\SEO::insertUriAlias($path, $_POST['mode'] == 1 ? '' : $alias, $keywords, $description, (int) $_POST['seo_robots']);
		}

		setMenuItemsCache();

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Seitentyp
	$values_mode = array(1, 2, 3);
	$lang_mode = array(
		ACP3\CMS::$injector['Lang']->t('menus', 'module'),
		ACP3\CMS::$injector['Lang']->t('menus', 'dynamic_page'),
		ACP3\CMS::$injector['Lang']->t('menus', 'hyperlink')
	);
	if (ACP3\Core\Modules::isActive('articles')) {
		$values_mode[] = 4;
		$lang_mode[] = ACP3\CMS::$injector['Lang']->t('menus', 'article');
	}
	ACP3\CMS::$injector['View']->assign('mode', ACP3\Core\Functions::selectGenerator('mode', $values_mode, $lang_mode));

	// Menus
	ACP3\CMS::$injector['View']->assign('blocks', menusDropdown());

	// Module
	$modules = ACP3\Core\Modules::getActiveModules();
	foreach ($modules as $row) {
		$modules[$row['name']]['selected'] = ACP3\Core\Functions::selectEntry('module', $row['dir']);
	}
	ACP3\CMS::$injector['View']->assign('modules', $modules);

	// Ziel des Hyperlinks
	$lang_target = array(ACP3\CMS::$injector['Lang']->t('system', 'window_self'), ACP3\CMS::$injector['Lang']->t('system', 'window_blank'));
	ACP3\CMS::$injector['View']->assign('target', ACP3\Core\Functions::selectGenerator('target', array(1, 2), $lang_target));

	$lang_display = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
	ACP3\CMS::$injector['View']->assign('display', ACP3\Core\Functions::selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

	if (ACP3\Core\Modules::check('articles', 'functions') === true) {
		require_once MODULES_DIR . 'articles/functions.php';

		ACP3\CMS::$injector['View']->assign('articles', articlesList());
	}

	$defaults = array(
		'title' => '',
		'alias' => '',
		'uri' => '',
		'seo_keywords' => '',
		'seo_description' => '',
	);

	// Daten an Smarty übergeben
	ACP3\CMS::$injector['View']->assign('pages_list', menuItemsList());
	ACP3\CMS::$injector['View']->assign('SEO_FORM_FIELDS', ACP3\Core\SEO::formFields());
	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
