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

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionCreate()
	{
		$settings = Core\Config::getSettings('news');

		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = $this->injector['Lang']->t('news', 'title_to_short');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = $this->injector['Lang']->t('news', 'text_to_short');
			if (strlen($_POST['cat_create']) < 3 && CategoriesFunctions::categoriesCheck($_POST['cat']) === false)
				$errors['cat'] = $this->injector['Lang']->t('news', 'select_category');
			if (strlen($_POST['cat_create']) >= 3 && CategoriesFunctions::categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
				$errors['cat-create'] = $this->injector['Lang']->t('categories', 'category_already_exists');
			if (!empty($_POST['link_title']) && (empty($_POST['uri']) || Core\Validate::isNumber($_POST['target']) === false))
				$errors[] = $this->injector['Lang']->t('news', 'complete_hyperlink_statements');
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
					(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias']) === true))
				$errors['alias'] = $this->injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'start' => $this->injector['Date']->toSQL($_POST['start']),
					'end' => $this->injector['Date']->toSQL($_POST['end']),
					'title' => Core\Functions::str_encode($_POST['title']),
					'text' => Core\Functions::str_encode($_POST['text'], true),
					'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
					'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
					'category_id' => strlen($_POST['cat_create']) >= 3 ? CategoriesFunctions::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
					'uri' => Core\Functions::str_encode($_POST['uri'], true),
					'target' => (int) $_POST['target'],
					'link_title' => Core\Functions::str_encode($_POST['link_title']),
					'user_id' => $this->injector['Auth']->getUserId(),
				);

				$bool = $this->injector['Db']->insert(DB_PRE . 'news', $insert_values);
				if ((bool) CONFIG_SEO_ALIASES === true)
					Core\SEO::insertUriAlias('news/details/id_' . $this->injector['Db']->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/news');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Datumsauswahl
			$this->injector['View']->assign('publication_period', $this->injector['Date']->datepicker(array('start', 'end')));

			// Kategorien
			$this->injector['View']->assign('categories', CategoriesFunctions::categoriesList('news', '', true));

			// Weiterlesen & Kommentare
			if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && Core\Modules::check('comments', 'functions') === true)) {
				$i = 0;
				$options = array();
				if ($settings['readmore'] == 1) {
					$options[$i]['name'] = 'readmore';
					$options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', '0', 'checked');
					$options[$i]['lang'] = $this->injector['Lang']->t('news', 'activate_readmore');
					$i++;
				}
				if ($settings['comments'] == 1 && Core\Modules::check('comments', 'functions') === true) {
					$options[$i]['name'] = 'comments';
					$options[$i]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
					$options[$i]['lang'] = $this->injector['Lang']->t('system', 'allow_comments');
				}
				$this->injector['View']->assign('options', $options);
			}

			// Linkziel
			$lang_target = array($this->injector['Lang']->t('system', 'window_self'), $this->injector['Lang']->t('system', 'window_blank'));
			$this->injector['View']->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target));

			$this->injector['View']->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => '', 'uri' => '', 'link_title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionDelete()
	{
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->injector['URI']->entries) === true)
			$entries = $this->injector['URI']->entries;

		if (!isset($entries)) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->injector['View']->setContent(confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/news/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/news')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$commentsInstalled = Core\Modules::isInstalled('comments');
			foreach ($marked_entries as $entry) {
				$bool = $this->injector['Db']->delete(DB_PRE . 'news', array('id' => $entry));
				if ($commentsInstalled === true)
					$this->injector['Db']->delete(DB_PRE . 'comments', array('module_id' => 'news', 'entry_id' => $entry));
				// News Cache löschen
				Core\Cache::delete('details_id_' . $entry, 'news');
				Core\SEO::deleteUriAlias('news/details/id_' . $entry);
			}
			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/news');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$settings = Core\Config::getSettings('news');

			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = $this->injector['Lang']->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = $this->injector['Lang']->t('news', 'title_to_short');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = $this->injector['Lang']->t('news', 'text_to_short');
				if (strlen($_POST['cat_create']) < 3 && CategoriesFunctions::categoriesCheck($_POST['cat']) === false)
					$errors['cat'] = $this->injector['Lang']->t('news', 'select_category');
				if (strlen($_POST['cat_create']) >= 3 && CategoriesFunctions::categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
					$errors['cat-create'] = $this->injector['Lang']->t('categories', 'category_already_exists');
				if (!empty($_POST['link_title']) && (empty($_POST['uri']) || Core\Validate::isNumber($_POST['target']) === false))
					$errors[] = $this->injector['Lang']->t('news', 'complete_additional_hyperlink_statements');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'news/details/id_' . $this->injector['URI']->id) === true))
					$errors['alias'] = $this->injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'start' => $this->injector['Date']->toSQL($_POST['start']),
						'end' => $this->injector['Date']->toSQL($_POST['end']),
						'title' => Core\Functions::str_encode($_POST['title']),
						'text' => Core\Functions::str_encode($_POST['text'], true),
						'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
						'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
						'category_id' => strlen($_POST['cat_create']) >= 3 ? CategoriesFunctions::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
						'uri' => Core\Functions::str_encode($_POST['uri'], true),
						'target' => (int) $_POST['target'],
						'link_title' => Core\Functions::str_encode($_POST['link_title']),
						'user_id' => $this->injector['Auth']->getUserId(),
					);

					$bool = $this->injector['Db']->update(DB_PRE . 'news', $update_values, array('id' => $this->injector['URI']->id));
					if ((bool) CONFIG_SEO_ALIASES === true)
						Core\SEO::insertUriAlias('news/details/id_' . $this->injector['URI']->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

					NewsFunctions::setNewsCache($this->injector['URI']->id);

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/news');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$news = $this->injector['Db']->fetchAssoc('SELECT start, end, title, text, readmore, comments, category_id, uri, target, link_title FROM ' . DB_PRE . 'news WHERE id = ?', array($this->injector['URI']->id));

				// Datumsauswahl
				$this->injector['View']->assign('publication_period', $this->injector['Date']->datepicker(array('start', 'end'), array($news['start'], $news['end'])));

				// Kategorien
				$this->injector['View']->assign('categories', CategoriesFunctions::categoriesList('news', $news['category_id'], true));

				// Weiterlesen & Kommentare
				if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && Core\Modules::check('comments', 'functions') === true)) {
					$i = 0;
					$options = array();
					if ($settings['readmore'] == 1) {
						$options[$i]['name'] = 'readmore';
						$options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', $news['readmore'], 'checked');
						$options[$i]['lang'] = $this->injector['Lang']->t('news', 'activate_readmore');
						$i++;
					}
					if ($settings['comments'] == 1 && Core\Modules::check('comments', 'functions') === true) {
						$options[$i]['name'] = 'comments';
						$options[$i]['checked'] = Core\Functions::selectEntry('comments', '1', $news['comments'], 'checked');
						$options[$i]['lang'] = $this->injector['Lang']->t('system', 'allow_comments');
					}
					$this->injector['View']->assign('options', $options);
				}

				// Linkziel
				$lang_target = array($this->injector['Lang']->t('system', 'window_self'), $this->injector['Lang']->t('system', 'window_blank'));
				$this->injector['View']->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target, $news['target']));

				$this->injector['View']->assign('SEO_FORM_FIELDS', Core\SEO::formFields('news/details/id_' . $this->injector['URI']->id));

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $news);

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$news = $this->injector['Db']->fetchAll('SELECT n.id, n.start, n.end, n.title, c.title AS cat FROM ' . DB_PRE . 'news AS n, ' . DB_PRE . 'categories AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
		$c_news = count($news);

		if ($c_news > 0) {
			$can_delete = Core\Modules::check('news', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));

			for ($i = 0; $i < $c_news; ++$i) {
				$news[$i]['period'] = $this->injector['Date']->formatTimeRange($news[$i]['start'], $news[$i]['end']);
			}
			$this->injector['View']->assign('news', $news);
			$this->injector['View']->assign('can_delete', $can_delete);
		}
	}

	public function actionSettings()
	{
		$comments_active = Core\Modules::isActive('comments');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = $this->injector['Lang']->t('system', 'select_date_format');
			if (Core\Validate::isNumber($_POST['sidebar']) === false)
				$errors['sidebar'] = $this->injector['Lang']->t('system', 'select_sidebar_entries');
			if (!isset($_POST['readmore']) || $_POST['readmore'] != 1 && $_POST['readmore'] != 0)
				$errors[] = $this->injector['Lang']->t('news', 'select_activate_readmore');
			if (Core\Validate::isNumber($_POST['readmore_chars']) === false || $_POST['readmore_chars'] == 0)
				$errors['readmore-chars'] = $this->injector['Lang']->t('news', 'type_in_readmore_chars');
			if (!isset($_POST['category_in_breadcrumb']) || $_POST['category_in_breadcrumb'] != 1 && $_POST['category_in_breadcrumb'] != 0)
				$errors[] = $this->injector['Lang']->t('news', 'select_display_category_in_breadcrumb');
			if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
				$errors[] = $this->injector['Lang']->t('news', 'select_allow_comments');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
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

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/news');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('news');

			$this->injector['View']->assign('dateformat', $this->injector['Date']->dateformatDropdown($settings['dateformat']));

			$lang_readmore = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('readmore', Core\Functions::selectGenerator('readmore', array(1, 0), $lang_readmore, $settings['readmore'], 'checked'));

			$this->injector['View']->assign('readmore_chars', isset($_POST['submit']) ? $_POST['readmore_chars'] : $settings['readmore_chars']);

			if ($comments_active === true) {
				$lang_allow_comments = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('allow_comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_allow_comments, $settings['comments'], 'checked'));
			}

			$this->injector['View']->assign('sidebar_entries', Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

			$lang_category_in_breadcrumb = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('category_in_breadcrumb', Core\Functions::selectGenerator('category_in_breadcrumb', array(1, 0), $lang_category_in_breadcrumb, $settings['category_in_breadcrumb'], 'checked'));

			$this->injector['Session']->generateFormToken();
		}
	}

}