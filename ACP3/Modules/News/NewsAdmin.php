<?php

namespace ACP3\Modules\News;

use ACP3\Core;
use ACP3\Modules\Categories\CategoriesFunctions;

/**
 * Description of NewsAdmin
 *
 * @author Tino
 */
class NewsAdmin extends Core\ModuleController {

	public function actionCreate()
	{
		$settings = Core\Config::getSettings('news');

		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = Core\Registry::get('Lang')->t('news', 'title_to_short');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = Core\Registry::get('Lang')->t('news', 'text_to_short');
			if (strlen($_POST['cat_create']) < 3 && CategoriesFunctions::categoriesCheck($_POST['cat']) === false)
				$errors['cat'] = Core\Registry::get('Lang')->t('news', 'select_category');
			if (strlen($_POST['cat_create']) >= 3 && CategoriesFunctions::categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
				$errors['cat-create'] = Core\Registry::get('Lang')->t('categories', 'category_already_exists');
			if (!empty($_POST['link_title']) && (empty($_POST['uri']) || Core\Validate::isNumber($_POST['target']) === false))
				$errors[] = Core\Registry::get('Lang')->t('news', 'complete_hyperlink_statements');
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
					'title' => Core\Functions::str_encode($_POST['title']),
					'text' => Core\Functions::str_encode($_POST['text'], true),
					'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
					'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
					'category_id' => strlen($_POST['cat_create']) >= 3 ? CategoriesFunctions::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
					'uri' => Core\Functions::str_encode($_POST['uri'], true),
					'target' => (int) $_POST['target'],
					'link_title' => Core\Functions::str_encode($_POST['link_title']),
					'user_id' => Core\Registry::get('Auth')->getUserId(),
				);

				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'news', $insert_values);
				if ((bool) CONFIG_SEO_ALIASES === true)
					Core\SEO::insertUriAlias('news/details/id_' . Core\Registry::get('Db')->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/news');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Datumsauswahl
			Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end')));

			// Kategorien
			Core\Registry::get('View')->assign('categories', CategoriesFunctions::categoriesList('news', '', true));

			// Weiterlesen & Kommentare
			if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true)) {
				$i = 0;
				$options = array();
				if ($settings['readmore'] == 1) {
					$options[$i]['name'] = 'readmore';
					$options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', '0', 'checked');
					$options[$i]['lang'] = Core\Registry::get('Lang')->t('news', 'activate_readmore');
					$i++;
				}
				if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
					$options[$i]['name'] = 'comments';
					$options[$i]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
					$options[$i]['lang'] = Core\Registry::get('Lang')->t('system', 'allow_comments');
				}
				Core\Registry::get('View')->assign('options', $options);
			}

			// Linkziel
			$lang_target = array(Core\Registry::get('Lang')->t('system', 'window_self'), Core\Registry::get('Lang')->t('system', 'window_blank'));
			Core\Registry::get('View')->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target));

			Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => '', 'uri' => '', 'link_title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

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
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/news/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/news')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$commentsInstalled = Core\Modules::isInstalled('comments');
			foreach ($marked_entries as $entry) {
				$bool = Core\Registry::get('Db')->delete(DB_PRE . 'news', array('id' => $entry));
				if ($commentsInstalled === true)
					Core\Registry::get('Db')->delete(DB_PRE . 'comments', array('module_id' => 'news', 'entry_id' => $entry));
				// News Cache löschen
				Core\Cache::delete('details_id_' . $entry, 'news');
				Core\SEO::deleteUriAlias('news/details/id_' . $entry);
			}
			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/news');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$settings = Core\Config::getSettings('news');

			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = Core\Registry::get('Lang')->t('news', 'title_to_short');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = Core\Registry::get('Lang')->t('news', 'text_to_short');
				if (strlen($_POST['cat_create']) < 3 && CategoriesFunctions::categoriesCheck($_POST['cat']) === false)
					$errors['cat'] = Core\Registry::get('Lang')->t('news', 'select_category');
				if (strlen($_POST['cat_create']) >= 3 && CategoriesFunctions::categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
					$errors['cat-create'] = Core\Registry::get('Lang')->t('categories', 'category_already_exists');
				if (!empty($_POST['link_title']) && (empty($_POST['uri']) || Core\Validate::isNumber($_POST['target']) === false))
					$errors[] = Core\Registry::get('Lang')->t('news', 'complete_additional_hyperlink_statements');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'news/details/id_' . Core\Registry::get('URI')->id) === true))
					$errors['alias'] = Core\Registry::get('Lang')->t('system', 'uri_alias_unallowed_characters_or_exists');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'start' => Core\Registry::get('Date')->toSQL($_POST['start']),
						'end' => Core\Registry::get('Date')->toSQL($_POST['end']),
						'title' => Core\Functions::str_encode($_POST['title']),
						'text' => Core\Functions::str_encode($_POST['text'], true),
						'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
						'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
						'category_id' => strlen($_POST['cat_create']) >= 3 ? CategoriesFunctions::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
						'uri' => Core\Functions::str_encode($_POST['uri'], true),
						'target' => (int) $_POST['target'],
						'link_title' => Core\Functions::str_encode($_POST['link_title']),
						'user_id' => Core\Registry::get('Auth')->getUserId(),
					);

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'news', $update_values, array('id' => Core\Registry::get('URI')->id));
					if ((bool) CONFIG_SEO_ALIASES === true)
						Core\SEO::insertUriAlias('news/details/id_' . Core\Registry::get('URI')->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

					NewsFunctions::setNewsCache(Core\Registry::get('URI')->id);

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/news');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$news = Core\Registry::get('Db')->fetchAssoc('SELECT start, end, title, text, readmore, comments, category_id, uri, target, link_title FROM ' . DB_PRE . 'news WHERE id = ?', array(Core\Registry::get('URI')->id));

				// Datumsauswahl
				Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end'), array($news['start'], $news['end'])));

				// Kategorien
				Core\Registry::get('View')->assign('categories', CategoriesFunctions::categoriesList('news', $news['category_id'], true));

				// Weiterlesen & Kommentare
				if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true)) {
					$i = 0;
					$options = array();
					if ($settings['readmore'] == 1) {
						$options[$i]['name'] = 'readmore';
						$options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', $news['readmore'], 'checked');
						$options[$i]['lang'] = Core\Registry::get('Lang')->t('news', 'activate_readmore');
						$i++;
					}
					if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
						$options[$i]['name'] = 'comments';
						$options[$i]['checked'] = Core\Functions::selectEntry('comments', '1', $news['comments'], 'checked');
						$options[$i]['lang'] = Core\Registry::get('Lang')->t('system', 'allow_comments');
					}
					Core\Registry::get('View')->assign('options', $options);
				}

				// Linkziel
				$lang_target = array(Core\Registry::get('Lang')->t('system', 'window_self'), Core\Registry::get('Lang')->t('system', 'window_blank'));
				Core\Registry::get('View')->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target, $news['target']));

				Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields('news/details/id_' . Core\Registry::get('URI')->id));

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $news);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$news = Core\Registry::get('Db')->fetchAll('SELECT n.id, n.start, n.end, n.title, c.title AS cat FROM ' . DB_PRE . 'news AS n, ' . DB_PRE . 'categories AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
		$c_news = count($news);

		if ($c_news > 0) {
			$can_delete = Core\Modules::check('news', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));

			for ($i = 0; $i < $c_news; ++$i) {
				$news[$i]['period'] = Core\Registry::get('Date')->formatTimeRange($news[$i]['start'], $news[$i]['end']);
			}
			Core\Registry::get('View')->assign('news', $news);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

	public function actionSettings()
	{
		$comments_active = Core\Modules::isActive('comments');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = Core\Registry::get('Lang')->t('system', 'select_date_format');
			if (Core\Validate::isNumber($_POST['sidebar']) === false)
				$errors['sidebar'] = Core\Registry::get('Lang')->t('system', 'select_sidebar_entries');
			if (!isset($_POST['readmore']) || $_POST['readmore'] != 1 && $_POST['readmore'] != 0)
				$errors[] = Core\Registry::get('Lang')->t('news', 'select_activate_readmore');
			if (Core\Validate::isNumber($_POST['readmore_chars']) === false || $_POST['readmore_chars'] == 0)
				$errors['readmore-chars'] = Core\Registry::get('Lang')->t('news', 'type_in_readmore_chars');
			if (!isset($_POST['category_in_breadcrumb']) || $_POST['category_in_breadcrumb'] != 1 && $_POST['category_in_breadcrumb'] != 0)
				$errors[] = Core\Registry::get('Lang')->t('news', 'select_display_category_in_breadcrumb');
			if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
				$errors[] = Core\Registry::get('Lang')->t('news', 'select_allow_comments');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'dateformat' => Core\Functions::str_encode($_POST['dateformat']),
					'sidebar' => (int) $_POST['sidebar'],
					'readmore' => $_POST['readmore'],
					'readmore_chars' => (int) $_POST['readmore_chars'],
					'category_in_breadcrumb' => $_POST['category_in_breadcrumb'],
					'comments' => $_POST['comments'],
				);
				$bool = Core\Config::setSettings('news', $data);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/news');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('news');

			Core\Registry::get('View')->assign('dateformat', Core\Registry::get('Date')->dateformatDropdown($settings['dateformat']));

			$lang_readmore = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('readmore', Core\Functions::selectGenerator('readmore', array(1, 0), $lang_readmore, $settings['readmore'], 'checked'));

			Core\Registry::get('View')->assign('readmore_chars', isset($_POST['submit']) ? $_POST['readmore_chars'] : $settings['readmore_chars']);

			if ($comments_active === true) {
				$lang_allow_comments = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('allow_comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_allow_comments, $settings['comments'], 'checked'));
			}

			Core\Registry::get('View')->assign('sidebar_entries', Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

			$lang_category_in_breadcrumb = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('category_in_breadcrumb', Core\Functions::selectGenerator('category_in_breadcrumb', array(1, 0), $lang_category_in_breadcrumb, $settings['category_in_breadcrumb'], 'checked'));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

}