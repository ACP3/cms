<?php

namespace ACP3\Modules\Articles;

use ACP3\Core;

/**
 * Module controller of the articles backend
 *
 * @author Tino Goratsch
 */
class ArticlesAdmin extends Core\ModuleController {

	public function actionCreate()
	{
		$access_to_menus = Core\Modules::hasPermission('menus', 'acp_create_item');

		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = Core\Registry::get('Lang')->t('articles', 'title_to_short');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = Core\Registry::get('Lang')->t('articles', 'text_to_short');
			if ($access_to_menus === true && isset($_POST['create']) === true) {
				if ($_POST['create'] == 1) {
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
				}
			}
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
					(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias']) === true))
				$errors['alias'] = Core\Registry::get('Lang')->t('system', 'uri_alias_unallowed_characters_or_exists');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'start' => Core\Registry::get('Date')->toSQL($_POST['start']),
					'end' => Core\Registry::get('Date')->toSQL($_POST['end']),
					'title' => Core\Functions::strEncode($_POST['title']),
					'text' => Core\Functions::strEncode($_POST['text'], true),
					'user_id' => Core\Registry::get('Auth')->getUserId(),
				);

				Core\Registry::get('Db')->beginTransaction();
				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'articles', $insert_values);
				$last_id = Core\Registry::get('Db')->lastInsertId();
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
					Core\SEO::insertUriAlias('articles/details/id_' . $last_id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
				Core\Registry::get('Db')->commit();

				if (isset($_POST['create']) === true && $access_to_menus === true) {
					$insert_values = array(
						'id' => '',
						'mode' => 4,
						'block_id' => $_POST['block_id'],
						'parent_id' => (int) $_POST['parent'],
						'display' => $_POST['display'],
						'title' => Core\Functions::strEncode($_POST['title']),
						'uri' => 'articles/details/id_' . $last_id . '/',
						'target' => 1,
					);

					$nestedSet = new Core\NestedSet('menu_items', true);
					$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
					\ACP3\Modules\Menus\MenusFunctions::setMenuItemsCache();
				}

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/articles');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			if ($access_to_menus === true) {
				$lang_options = array(Core\Registry::get('Lang')->t('articles', 'create_menu_item'));
				Core\Registry::get('View')->assign('options', Core\Functions::selectGenerator('create', array(1), $lang_options, 0, 'checked'));

				// Block
				Core\Registry::get('View')->assign('blocks', \ACP3\Modules\Menus\MenusFunctions::menusDropdown());

				$lang_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('display', Core\Functions::selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

				Core\Registry::get('View')->assign('pages_list', \ACP3\Modules\Menus\MenusFunctions::menuItemsList());
			}

			Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end')));

			$defaults = array(
				'title' => '',
				'text' => '',
				'alias' => '',
				'seo_keywords' => '',
				'seo_description' => ''
			);

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
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/articles/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/articles')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$nestedSet = new Core\NestedSet('menu_items', true);
			foreach ($marked_entries as $entry) {
				$bool = Core\Registry::get('Db')->delete(DB_PRE . 'articles', array('id' => $entry));
				$nestedSet->deleteNode(Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'menu_items WHERE uri = ?', array('articles/details/id_' . $entry . '/')));

				Core\Cache::delete('list_id_' . $entry, 'articles');
				Core\SEO::deleteUriAlias('articles/details/id_' . $entry);
			}

			if (Core\Modules::isInstalled('menus') === true) {
				\ACP3\Modules\Menus\MenusFunctions::setMenuItemsCache();
			}

			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/articles');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = Core\Registry::get('Lang')->t('articles', 'title_to_short');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = Core\Registry::get('Lang')->t('articles', 'text_to_short');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'articles/details/id_' . Core\Registry::get('URI')->id) === true))
					$errors['alias'] = Core\Registry::get('Lang')->t('system', 'uri_alias_unallowed_characters_or_exists');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'start' => Core\Registry::get('Date')->toSQL($_POST['start']),
						'end' => Core\Registry::get('Date')->toSQL($_POST['end']),
						'title' => Core\Functions::strEncode($_POST['title']),
						'text' => Core\Functions::strEncode($_POST['text'], true),
						'user_id' => Core\Registry::get('Auth')->getUserId(),
					);

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'articles', $update_values, array('id' => Core\Registry::get('URI')->id));
					if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
						Core\SEO::insertUriAlias('articles/details/id_' . Core\Registry::get('URI')->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

					ArticlesFunctions::setArticlesCache(Core\Registry::get('URI')->id);

					// Aliase in der Navigation aktualisieren
					\ACP3\Modules\Menus\MenusFunctions::setMenuItemsCache();

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$page = ArticlesFunctions::getArticlesCache(Core\Registry::get('URI')->id);

				// Datumsauswahl
				Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end'), array($page['start'], $page['end'])));

				Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields('articles/details/id_' . Core\Registry::get('URI')->id));

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

		$articles = Core\Registry::get('Db')->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles ORDER BY title ASC');
		$c_articles = count($articles);

		if ($c_articles > 0) {
			$can_delete = Core\Modules::hasPermission('articles', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 2 : 1,
				'sort_dir' => 'asc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_articles; ++$i) {
				$articles[$i]['period'] = Core\Registry::get('Date')->formatTimeRange($articles[$i]['start'], $articles[$i]['end']);
			}
			Core\Registry::get('View')->assign('articles', $articles);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

}