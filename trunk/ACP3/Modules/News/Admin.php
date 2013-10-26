<?php

namespace ACP3\Modules\News;

use ACP3\Core;
use ACP3\Modules\Categories;

/**
 * Description of NewsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller {

	public function __construct() {
		parent::__construct();
	}

	public function actionCreate()
	{
		$settings = Core\Config::getSettings('news');

		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = $this->lang->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = $this->lang->t('news', 'title_to_short');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = $this->lang->t('news', 'text_to_short');
			if (strlen($_POST['cat_create']) < 3 && Categories\Helpers::categoriesCheck($_POST['cat']) === false)
				$errors['cat'] = $this->lang->t('news', 'select_category');
			if (strlen($_POST['cat_create']) >= 3 && Categories\Helpers::categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
				$errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
			if (!empty($_POST['link_title']) && (empty($_POST['uri']) || Core\Validate::isNumber($_POST['target']) === false))
				$errors[] = $this->lang->t('news', 'complete_hyperlink_statements');
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
					(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias']) === true))
				$errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'start' => $this->date->toSQL($_POST['start']),
					'end' => $this->date->toSQL($_POST['end']),
					'title' => Core\Functions::strEncode($_POST['title']),
					'text' => Core\Functions::strEncode($_POST['text'], true),
					'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
					'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
					'category_id' => strlen($_POST['cat_create']) >= 3 ? Categories\Helpers::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
					'uri' => Core\Functions::strEncode($_POST['uri'], true),
					'target' => (int) $_POST['target'],
					'link_title' => Core\Functions::strEncode($_POST['link_title']),
					'user_id' => $this->auth->getUserId(),
				);

				$bool = $this->db->insert(DB_PRE . 'news', $insert_values);
				if ((bool) CONFIG_SEO_ALIASES === true)
					Core\SEO::insertUriAlias('news/details/id_' . $this->db->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/news');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Datumsauswahl
			$this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));

			// Kategorien
			$this->view->assign('categories', Categories\Helpers::categoriesList('news', '', true));

			// Weiterlesen & Kommentare
			if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true)) {
				$i = 0;
				$options = array();
				if ($settings['readmore'] == 1) {
					$options[$i]['name'] = 'readmore';
					$options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', '0', 'checked');
					$options[$i]['lang'] = $this->lang->t('news', 'activate_readmore');
					$i++;
				}
				if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
					$options[$i]['name'] = 'comments';
					$options[$i]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
					$options[$i]['lang'] = $this->lang->t('system', 'allow_comments');
				}
				$this->view->assign('options', $options);
			}

			// Linkziel
			$lang_target = array($this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank'));
			$this->view->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target));

			$this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

			$this->view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => '', 'uri' => '', 'link_title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

			$this->session->generateFormToken();
		}
	}

	public function actionDelete()
	{
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->uri->entries) === true)
			$entries = $this->uri->entries;

		if (!isset($entries)) {
			$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->view->setContent(Core\Functions::confirmBox($this->lang->t('system', 'confirm_delete'), $this->uri->route('acp/news/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->uri->route('acp/news')));
		} elseif ($this->uri->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$commentsInstalled = Core\Modules::isInstalled('comments');
			foreach ($marked_entries as $entry) {
				$bool = $this->db->delete(DB_PRE . 'news', array('id' => $entry));
				if ($commentsInstalled === true)
					$this->db->delete(DB_PRE . 'comments', array('module_id' => 'news', 'entry_id' => $entry));
				// News Cache löschen
				Core\Cache::delete('details_id_' . $entry, 'news');
				Core\SEO::deleteUriAlias('news/details/id_' . $entry);
			}
			Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/news');
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE id = ?', array($this->uri->id)) == 1) {
			$settings = Core\Config::getSettings('news');

			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = $this->lang->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = $this->lang->t('news', 'title_to_short');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = $this->lang->t('news', 'text_to_short');
				if (strlen($_POST['cat_create']) < 3 && Categories\Helpers::categoriesCheck($_POST['cat']) === false)
					$errors['cat'] = $this->lang->t('news', 'select_category');
				if (strlen($_POST['cat_create']) >= 3 && Categories\Helpers::categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
					$errors['cat-create'] = $this->lang->t('categories', 'category_already_exists');
				if (!empty($_POST['link_title']) && (empty($_POST['uri']) || Core\Validate::isNumber($_POST['target']) === false))
					$errors[] = $this->lang->t('news', 'complete_additional_hyperlink_statements');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'news/details/id_' . $this->uri->id) === true))
					$errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');

				if (isset($errors) === true) {
					$this->view->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'start' => $this->date->toSQL($_POST['start']),
						'end' => $this->date->toSQL($_POST['end']),
						'title' => Core\Functions::strEncode($_POST['title']),
						'text' => Core\Functions::strEncode($_POST['text'], true),
						'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
						'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
						'category_id' => strlen($_POST['cat_create']) >= 3 ? Categories\Helpers::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
						'uri' => Core\Functions::strEncode($_POST['uri'], true),
						'target' => (int) $_POST['target'],
						'link_title' => Core\Functions::strEncode($_POST['link_title']),
						'user_id' => $this->auth->getUserId(),
					);

					$bool = $this->db->update(DB_PRE . 'news', $update_values, array('id' => $this->uri->id));
					if ((bool) CONFIG_SEO_ALIASES === true)
						Core\SEO::insertUriAlias('news/details/id_' . $this->uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

					Helpers::setNewsCache($this->uri->id);

					$this->session->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/news');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$news = $this->db->fetchAssoc('SELECT start, end, title, text, readmore, comments, category_id, uri, target, link_title FROM ' . DB_PRE . 'news WHERE id = ?', array($this->uri->id));

				// Datumsauswahl
				$this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($news['start'], $news['end'])));

				// Kategorien
				$this->view->assign('categories', Categories\Helpers::categoriesList('news', $news['category_id'], true));

				// Weiterlesen & Kommentare
				if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true)) {
					$i = 0;
					$options = array();
					if ($settings['readmore'] == 1) {
						$options[$i]['name'] = 'readmore';
						$options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', $news['readmore'], 'checked');
						$options[$i]['lang'] = $this->lang->t('news', 'activate_readmore');
						$i++;
					}
					if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
						$options[$i]['name'] = 'comments';
						$options[$i]['checked'] = Core\Functions::selectEntry('comments', '1', $news['comments'], 'checked');
						$options[$i]['lang'] = $this->lang->t('system', 'allow_comments');
					}
					$this->view->assign('options', $options);
				}

				// Linkziel
				$lang_target = array($this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank'));
				$this->view->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target, $news['target']));

				$this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields('news/details/id_' . $this->uri->id));

				$this->view->assign('form', isset($_POST['submit']) ? $_POST : $news);

				$this->session->generateFormToken();
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$news = $this->db->fetchAll('SELECT n.id, n.start, n.end, n.title, c.title AS cat FROM ' . DB_PRE . 'news AS n, ' . DB_PRE . 'categories AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
		$c_news = count($news);

		if ($c_news > 0) {
			$can_delete = Core\Modules::hasPermission('news', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->view->appendContent(Core\Functions::datatable($config));

			for ($i = 0; $i < $c_news; ++$i) {
				$news[$i]['period'] = $this->date->formatTimeRange($news[$i]['start'], $news[$i]['end']);
			}
			$this->view->assign('news', $news);
			$this->view->assign('can_delete', $can_delete);
		}
	}

	public function actionSettings()
	{
		$comments_active = Core\Modules::isActive('comments');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = $this->lang->t('system', 'select_date_format');
			if (Core\Validate::isNumber($_POST['sidebar']) === false)
				$errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
			if (!isset($_POST['readmore']) || $_POST['readmore'] != 1 && $_POST['readmore'] != 0)
				$errors[] = $this->lang->t('news', 'select_activate_readmore');
			if (Core\Validate::isNumber($_POST['readmore_chars']) === false || $_POST['readmore_chars'] == 0)
				$errors['readmore-chars'] = $this->lang->t('news', 'type_in_readmore_chars');
			if (!isset($_POST['category_in_breadcrumb']) || $_POST['category_in_breadcrumb'] != 1 && $_POST['category_in_breadcrumb'] != 0)
				$errors[] = $this->lang->t('news', 'select_display_category_in_breadcrumb');
			if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
				$errors[] = $this->lang->t('news', 'select_allow_comments');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
					'sidebar' => (int) $_POST['sidebar'],
					'readmore' => $_POST['readmore'],
					'readmore_chars' => (int) $_POST['readmore_chars'],
					'category_in_breadcrumb' => $_POST['category_in_breadcrumb'],
					'comments' => $_POST['comments'],
				);
				$bool = Core\Config::setSettings('news', $data);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/news');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('news');

			$this->view->assign('dateformat', $this->date->dateformatDropdown($settings['dateformat']));

			$lang_readmore = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('readmore', Core\Functions::selectGenerator('readmore', array(1, 0), $lang_readmore, $settings['readmore'], 'checked'));

			$this->view->assign('readmore_chars', isset($_POST['submit']) ? $_POST['readmore_chars'] : $settings['readmore_chars']);

			if ($comments_active === true) {
				$lang_allow_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
				$this->view->assign('allow_comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_allow_comments, $settings['comments'], 'checked'));
			}

			$this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

			$lang_category_in_breadcrumb = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('category_in_breadcrumb', Core\Functions::selectGenerator('category_in_breadcrumb', array(1, 0), $lang_category_in_breadcrumb, $settings['category_in_breadcrumb'], 'checked'));

			$this->session->generateFormToken();
		}
	}

}