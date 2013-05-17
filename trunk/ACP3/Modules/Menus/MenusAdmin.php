<?php

namespace ACP3\Modules\Menus;

use ACP3\Core;

/**
 * Description of MenusAdmin
 *
 * @author Tino
 */
class MenusAdmin extends Core\ModuleController {

	public function actionCreate()
	{
		if (isset($_POST['submit']) === true) {
			if (!preg_match('/^[a-zA-Z]+\w/', $_POST['index_name']))
				$errors['index-name'] = Core\Registry::get('Lang')->t('menus', 'type_in_index_name');
			if (!isset($errors) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE index_name = ?', array($_POST['index_name'])) > 0)
				$errors['index-name'] = Core\Registry::get('Lang')->t('menus', 'index_name_unique');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = Core\Registry::get('Lang')->t('menus', 'menu_bar_title_to_short');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'index_name' => $_POST['index_name'],
					'title' => Core\Functions::str_encode($_POST['title']),
				);

				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'menus', $insert_values);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('index_name' => '', 'title' => ''));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionCreate_item()
	{
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::isNumber($_POST['mode']) === false)
				$errors['mode'] = Core\Registry::get('Lang')->t('menus', 'select_page_type');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = Core\Registry::get('Lang')->t('menus', 'title_to_short');
			if (Core\Validate::isNumber($_POST['block_id']) === false)
				$errors['block-id'] = Core\Registry::get('Lang')->t('menus', 'select_menu_bar');
			if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === false)
				$errors['parent'] = Core\Registry::get('Lang')->t('menus', 'select_superior_page');
			if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === true) {
				// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
				$parent_block = Core\Registry::get('Db')->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($_POST['parent']));
				if (!empty($parent_block) && $parent_block != $_POST['block_id'])
					$errors['parent'] = Core\Registry::get('Lang')->t('menus', 'superior_page_not_allowed');
			}
			if ($_POST['display'] != 0 && $_POST['display'] != 1)
				$errors[] = Core\Registry::get('Lang')->t('menus', 'select_item_visibility');
			if (Core\Validate::isNumber($_POST['target']) === false ||
					$_POST['mode'] == 1 && (is_dir(MODULES_DIR . $_POST['module']) === false || preg_match('=/=', $_POST['module'])) ||
					$_POST['mode'] == 2 && Core\Validate::isInternalURI($_POST['uri']) === false ||
					$_POST['mode'] == 3 && empty($_POST['uri']) ||
					$_POST['mode'] == 4 && (Core\Validate::isNumber($_POST['articles']) === false || Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array($_POST['articles'])) == 0))
				$errors[] = Core\Registry::get('Lang')->t('menus', 'type_in_uri_and_target');
			if ($_POST['mode'] == 2 && (bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
					(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias']) === true))
				$errors['alias'] = Core\Registry::get('Lang')->t('system', 'uri_alias_unallowed_characters_or_exists');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'mode' => ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match('/^(articles\/details\/id_([0-9]+)\/)$/', $_POST['uri']) ? '4' : $_POST['mode'],
					'block_id' => (int) $_POST['block_id'],
					'parent_id' => (int) $_POST['parent'],
					'display' => $_POST['display'],
					'title' => Core\Functions::str_encode($_POST['title']),
					'uri' => $_POST['mode'] == 1 ? $_POST['module'] : ($_POST['mode'] == 4 ? 'articles/details/id_' . $_POST['articles'] . '/' : $_POST['uri']),
					'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
				);

				$nestedSet = new Core\NestedSet('menu_items', true);
				$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);

				// Verhindern, dass externen URIs Aliase, Keywords, etc. zugewiesen bekommen
				if ($_POST['mode'] != 3) {
					$path = $_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'];
					if (Core\SEO::uriAliasExists($_POST['uri'])) {
						$alias = !empty($_POST['alias']) ? $_POST['alias'] : Core\SEO::getUriAlias($_POST['uri']);
						$keywords = Core\SEO::getKeywords($_POST['uri']);
						$description = Core\SEO::getDescription($_POST['uri']);
					} else {
						$alias = $_POST['alias'];
						$keywords = $_POST['seo_keywords'];
						$description = $_POST['seo_description'];
					}
					Core\SEO::insertUriAlias($path, $_POST['mode'] == 1 ? '' : $alias, $keywords, $description, (int) $_POST['seo_robots']);
				}

				MenusFunctions::setMenuItemsCache();

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Seitentyp
			$values_mode = array(1, 2, 3);
			$lang_mode = array(
				Core\Registry::get('Lang')->t('menus', 'module'),
				Core\Registry::get('Lang')->t('menus', 'dynamic_page'),
				Core\Registry::get('Lang')->t('menus', 'hyperlink')
			);
			if (Core\Modules::isActive('articles')) {
				$values_mode[] = 4;
				$lang_mode[] = Core\Registry::get('Lang')->t('menus', 'article');
			}
			Core\Registry::get('View')->assign('mode', Core\Functions::selectGenerator('mode', $values_mode, $lang_mode));

			// Menus
			Core\Registry::get('View')->assign('blocks', MenusFunctions::menusDropdown());

			// Module
			$modules = Core\Modules::getActiveModules();
			foreach ($modules as $row) {
				$modules[$row['name']]['selected'] = Core\Functions::selectEntry('module', $row['dir']);
			}
			Core\Registry::get('View')->assign('modules', $modules);

			// Ziel des Hyperlinks
			$lang_target = array(Core\Registry::get('Lang')->t('system', 'window_self'), Core\Registry::get('Lang')->t('system', 'window_blank'));
			Core\Registry::get('View')->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target));

			$lang_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('display', Core\Functions::selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

			if (Core\Modules::isActive('articles') === true) {
				Core\Registry::get('View')->assign('articles', \ACP3\Modules\Articles\ArticlesFunctions::articlesList());
			}

			$defaults = array(
				'title' => '',
				'alias' => '',
				'uri' => '',
				'seo_keywords' => '',
				'seo_description' => '',
			);

			// Daten an Smarty übergeben
			Core\Registry::get('View')->assign('pages_list', MenusFunctions::menuItemsList());
			Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields());
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionDelete()
	{
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (Core\Registry::get('URI')->action !== 'confirmed') {
			$marked_entries = implode('|', (array) $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/menus/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/menus')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$nestedSet = new Core\NestedSet('menu_items', true);
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE id = ?', array($entry)) == 1) {
					// Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
					$items = Core\Registry::get('Db')->fetchAll('SELECT id FROM ' . DB_PRE . 'menu_items WHERE block_id = ?', array($entry));
					foreach ($items as $row) {
						$nestedSet->deleteNode($row['id']);
					}

					$block = Core\Registry::get('Db')->fetchColumn('SELECT index_name FROM ' . DB_PRE . 'menus WHERE id = ?', array($entry));
					$bool = Core\Registry::get('Db')->delete(DB_PRE . 'menus', array('id' => $entry));
					Core\Cache::delete('visible_items_' . $block, 'menus');
				}
			}

			MenusFunctions::setMenuItemsCache();

			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionDelete_item()
	{
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/menus/delete_item/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/menus')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$nestedSet = new Core\NestedSet('menu_items', true);
			foreach ($marked_entries as $entry) {
				// URI-Alias löschen
				$item_uri = Core\Registry::get('Db')->fetchColumn('SELECT uri FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($entry));
				$bool = $nestedSet->deleteNode($entry);
				Core\SEO::deleteUriAlias($item_uri);
			}

			MenusFunctions::setMenuItemsCache();

			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (!preg_match('/^[a-zA-Z]+\w/', $_POST['index_name']))
					$errors['index-name'] = Core\Registry::get('Lang')->t('menus', 'type_in_index_name');
				if (!isset($errors) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE index_name = ? AND id != ?', array($_POST['index_name'], Core\Registry::get('URI')->id)) > 0)
					$errors['index-name'] = Core\Registry::get('Lang')->t('menus', 'index_name_unique');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = Core\Registry::get('Lang')->t('menus', 'menu_bar_title_to_short');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'index_name' => $_POST['index_name'],
						'title' => Core\Functions::str_encode($_POST['title']),
					);

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'menus', $update_values, array('id' => Core\Registry::get('URI')->id));

					MenusFunctions::setMenuItemsCache();

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$block = Core\Registry::get('Db')->fetchAssoc('SELECT index_name, title FROM ' . DB_PRE . 'menus WHERE id = ?', array(Core\Registry::get('URI')->id));

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $block);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit_item()
	{
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menu_items WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$page = Core\Registry::get('Db')->fetchAssoc('SELECT id, mode, block_id, parent_id, left_id, right_id, display, title, uri, target FROM ' . DB_PRE . 'menu_items WHERE id = ?', array(Core\Registry::get('URI')->id));
			$page['alias'] = $page['mode'] == 2 || $page['mode'] == 4 ? Core\SEO::getUriAlias($page['uri'], true) : '';
			$page['seo_keywords'] = Core\SEO::getKeywords($page['uri']);
			$page['seo_description'] = Core\SEO::getDescription($page['uri']);

			if (isset($_POST['submit']) === true) {
				if (Core\Validate::isNumber($_POST['mode']) === false)
					$errors['mode'] = Core\Registry::get('Lang')->t('menus', 'select_page_type');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = Core\Registry::get('Lang')->t('menus', 'title_to_short');
				if (Core\Validate::isNumber($_POST['block_id']) === false)
					$errors['block-id'] = Core\Registry::get('Lang')->t('menus', 'select_menu_bar');
				if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === false)
					$errors['parent'] = Core\Registry::get('Lang')->t('menus', 'select_superior_page');
				if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === true) {
					// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
					$parent_block = Core\Registry::get('Db')->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($_POST['parent']));
					if (!empty($parent_block) && $parent_block != $_POST['block_id'])
						$errors[] = Core\Registry::get('Lang')->t('menus', 'superior_page_not_allowed');
				}
				if ($_POST['display'] != 0 && $_POST['display'] != 1)
					$errors['display'] = Core\Registry::get('Lang')->t('menus', 'select_item_visibility');
				if (Core\Validate::isNumber($_POST['target']) === false ||
						$_POST['mode'] == 1 && (is_dir(MODULES_DIR . $_POST['module']) === false || preg_match('=/=', $_POST['module'])) ||
						$_POST['mode'] == 2 && Core\Validate::isInternalURI($_POST['uri']) === false ||
						$_POST['mode'] == 3 && empty($_POST['uri']) ||
						$_POST['mode'] == 4 && (Core\Validate::isNumber($_POST['articles']) === false || Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array($_POST['articles'])) == 0))
					$errors[] = Core\Registry::get('Lang')->t('menus', 'type_in_uri_and_target');
				if (($_POST['mode'] == 2 || $_POST['mode'] == 4) && (bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], $_POST['uri'])))
					$errors['alias'] = Core\Registry::get('Lang')->t('system', 'uri_alias_unallowed_characters_or_exists');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					// Vorgenommene Änderungen am Datensatz anwenden
					$mode = ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match('/^(articles\/details\/id_([0-9]+)\/)$/', $_POST['uri']) ? '4' : $_POST['mode'];
					$uri_type = $_POST['mode'] == 4 ? 'articles/details/id_' . $_POST['articles'] . '/' : $_POST['uri'];

					$update_values = array(
						'mode' => $mode,
						'block_id' => $_POST['block_id'],
						'parent_id' => $_POST['parent'],
						'display' => $_POST['display'],
						'title' => Core\Functions::str_encode($_POST['title']),
						'uri' => $_POST['mode'] == 1 ? $_POST['module'] : $uri_type,
						'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
					);

					$nestedSet = new Core\NestedSet('menu_items', true);
					$bool = $nestedSet->editNode(Core\Registry::get('URI')->id, (int) $_POST['parent'], (int) $_POST['block_id'], $update_values);

					// Verhindern, dass externen URIs Aliase, Keywords, etc. zugewiesen bekommen
					if ($_POST['mode'] != 3) {
						$alias = $_POST['alias'] === $page['alias'] ? $page['alias'] : $_POST['alias'];
						$keywords = $_POST['seo_keywords'] === $page['seo_keywords'] ? $page['seo_keywords'] : $_POST['seo_keywords'];
						$description = $_POST['seo_description'] === $page['seo_description'] ? $page['seo_description'] : $_POST['seo_description'];
						$path = $_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'];
						Core\SEO::insertUriAlias($path, $_POST['mode'] == 1 ? '' : $alias, $keywords, $description, (int) $_POST['seo_robots']);
					}

					MenusFunctions::setMenuItemsCache();

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				// Seitentyp
				$values_mode = array(1, 2, 3);
				$lang_mode = array(
					Core\Registry::get('Lang')->t('menus', 'module'),
					Core\Registry::get('Lang')->t('menus', 'dynamic_page'),
					Core\Registry::get('Lang')->t('menus', 'hyperlink')
				);
				if (Core\Modules::isActive('articles')) {
					$values_mode[] = 4;
					$lang_mode[] = Core\Registry::get('Lang')->t('menus', 'article');
				}
				Core\Registry::get('View')->assign('mode', Core\Functions::selectGenerator('mode', $values_mode, $lang_mode, $page['mode']));

				// Block
				Core\Registry::get('View')->assign('blocks', MenusFunctions::menusDropdown($page['block_id']));

				// Module
				$modules = Core\Modules::getAllModules();
				foreach ($modules as $row) {
					$modules[$row['name']]['selected'] = Core\Functions::selectEntry('module', $row['dir'], $page['mode'] == 1 ? $page['uri'] : '');
				}
				Core\Registry::get('View')->assign('modules', $modules);

				// Ziel des Hyperlinks
				$lang_target = array(Core\Registry::get('Lang')->t('system', 'window_self'), Core\Registry::get('Lang')->t('system', 'window_blank'));
				Core\Registry::get('View')->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target, $page['target']));

				$lang_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('display', Core\Functions::selectGenerator('display', array(1, 0), $lang_display, $page['display'], 'checked'));

				if (Core\Modules::isActive('articles') === true) {
					$matches = array();
					if (!isset($_POST['submit']) && $page['mode'] == 4) {
						preg_match_all('/^(articles\/details\/id_([0-9]+)\/)$/', $page['uri'], $matches);
					}

					Core\Registry::get('View')->assign('articles', \ACP3\Modules\Articles\ArticlesFunctions::articlesList(!empty($matches[2]) ? $matches[2][0] : ''));
				}

				// Daten an Smarty übergeben
				Core\Registry::get('View')->assign('pages_list', MenusFunctions::menuItemsList($page['parent_id'], $page['left_id'], $page['right_id']));
				Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields($page['uri']));
				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $page);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$menus = Core\Registry::get('Db')->fetchAll('SELECT id, title, index_name FROM ' . DB_PRE . 'menus');
		$c_menus = count($menus);

		if ($c_menus > 0) {
			$can_delete_item = Core\Modules::check('menus', 'acp_delete_item');
			$can_order_item = Core\Modules::check('menus', 'acp_order');
			Core\Registry::get('View')->assign('can_delete_item', $can_delete_item);
			Core\Registry::get('View')->assign('can_order_item', $can_order_item);
			Core\Registry::get('View')->assign('can_delete', Core\Modules::check('menus', 'acp_delete'));
			Core\Registry::get('View')->assign('can_edit', Core\Modules::check('menus', 'acp_edit'));
			Core\Registry::get('View')->assign('colspan', $can_delete_item && $can_order_item ? 5 : ($can_delete_item || $can_order_item ? 4 : 3));

			$pages_list = MenusFunctions::menuItemsList();
			for ($i = 0; $i < $c_menus; ++$i) {
				if (isset($pages_list[$menus[$i]['index_name']]) === false) {
					$pages_list[$menus[$i]['index_name']]['title'] = $menus[$i]['title'];
					$pages_list[$menus[$i]['index_name']]['menu_id'] = $menus[$i]['id'];
					$pages_list[$menus[$i]['index_name']]['items'] = array();
				}
			}
			Core\Registry::get('View')->assign('pages_list', $pages_list);
		}
	}

	public function actionOrder()
	{
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menu_items WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$nestedSet = new Core\NestedSet('menu_items', true);
			$nestedSet->order(Core\Registry::get('URI')->id, Core\Registry::get('URI')->action);

			MenusFunctions::setMenuItemsCache();

			Core\Registry::get('URI')->redirect('acp/menus');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

}