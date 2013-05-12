<?php

namespace ACP3\Modules\Articles;

use ACP3\Core;

/**
 * Description of ArticlesAdmin
 *
 * @author Tino
 */
class ArticlesAdmin extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionCreate()
	{
		$access_to_menus = Core\Modules::check('menus', 'acp_create_item');

		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = $this->injector['Lang']->t('articles', 'title_to_short');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = $this->injector['Lang']->t('articles', 'text_to_short');
			if ($access_to_menus === true && isset($_POST['create']) === true) {
				if ($_POST['create'] == 1) {
					if (Core\Validate::isNumber($_POST['block_id']) === false)
						$errors['block-id'] = $this->injector['Lang']->t('menus', 'select_menu_bar');
					if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === false)
						$errors['parent'] = $this->injector['Lang']->t('menus', 'select_superior_page');
					if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === true) {
						// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
						$parent_block = $this->injector['Db']->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($_POST['parent']));
						if (!empty($parent_block) && $parent_block != $_POST['block_id'])
							$errors['parent'] = $this->injector['Lang']->t('menus', 'superior_page_not_allowed');
					}
					if ($_POST['display'] != 0 && $_POST['display'] != 1)
						$errors[] = $this->injector['Lang']->t('menus', 'select_item_visibility');
				}
			}
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
					'user_id' => $this->injector['Auth']->getUserId(),
				);

				$this->injector['Db']->beginTransaction();
				$bool = $this->injector['Db']->insert(DB_PRE . 'articles', $insert_values);
				$last_id = $this->injector['Db']->lastInsertId();
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
					Core\SEO::insertUriAlias('articles/details/id_' . $last_id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
				$this->injector['Db']->commit();

				if (isset($_POST['create']) === true && $access_to_menus === true) {
					$insert_values = array(
						'id' => '',
						'mode' => 4,
						'block_id' => $_POST['block_id'],
						'parent_id' => (int) $_POST['parent'],
						'display' => $_POST['display'],
						'title' => Core\Functions::str_encode($_POST['title']),
						'uri' => 'articles/details/id_' . $last_id . '/',
						'target' => 1,
					);

					$nestedSet = new Core\NestedSet('menu_items', true);
					$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
					\ACP3\Modules\Menus\MenusFunctions::setMenuItemsCache();
				}

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/articles');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			if ($access_to_menus === true) {
				$lang_options = array($this->injector['Lang']->t('articles', 'create_menu_item'));
				$this->injector['View']->assign('options', Core\Functions::selectGenerator('create', array(1), $lang_options, 0, 'checked'));

				// Block
				$this->injector['View']->assign('blocks', \ACP3\Modules\Menus\MenusFunctions::menusDropdown());

				$lang_display = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('display', Core\Functions::selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

				$this->injector['View']->assign('pages_list', \ACP3\Modules\Menus\MenusFunctions::menuItemsList());
			}

			$this->injector['View']->assign('publication_period', $this->injector['Date']->datepicker(array('start', 'end')));

			$defaults = array(
				'title' => '',
				'text' => '',
				'alias' => '',
				'seo_keywords' => '',
				'seo_description' => ''
			);

			$this->injector['View']->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

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
			$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/articles/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/articles')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$nestedSet = new Core\NestedSet('menu_items', true);
			foreach ($marked_entries as $entry) {
				$bool = $this->injector['Db']->delete(DB_PRE . 'articles', array('id' => $entry));
				$nestedSet->deleteNode($this->injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'menu_items WHERE uri = ?', array('articles/details/id_' . $entry . '/')));

				Core\Cache::delete('list_id_' . $entry, 'articles');
				Core\SEO::deleteUriAlias('articles/details/id_' . $entry);
			}

			if (Core\Modules::isInstalled('menus') === true) {
				\ACP3\Modules\Menus\MenusFunctions::setMenuItemsCache();
			}

			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/articles');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = $this->injector['Lang']->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = $this->injector['Lang']->t('articles', 'title_to_short');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = $this->injector['Lang']->t('articles', 'text_to_short');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'articles/details/id_' . $this->injector['URI']->id) === true))
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
						'user_id' => $this->injector['Auth']->getUserId(),
					);

					$bool = $this->injector['Db']->update(DB_PRE . 'articles', $update_values, array('id' => $this->injector['URI']->id));
					if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
						Core\SEO::insertUriAlias('articles/details/id_' . $this->injector['URI']->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

					ArticlesFunctions::setArticlesCache($this->injector['URI']->id);

					// Aliase in der Navigation aktualisieren
					\ACP3\Modules\Menus\MenusFunctions::setMenuItemsCache();

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$page = ArticlesFunctions::getArticlesCache($this->injector['URI']->id);

				// Datumsauswahl
				$this->injector['View']->assign('publication_period', $this->injector['Date']->datepicker(array('start', 'end'), array($page['start'], $page['end'])));

				$this->injector['View']->assign('SEO_FORM_FIELDS', Core\SEO::formFields('articles/details/id_' . $this->injector['URI']->id));

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $page);

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$articles = $this->injector['Db']->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles ORDER BY title ASC');
		$c_articles = count($articles);

		if ($c_articles > 0) {
			$can_delete = Core\Modules::check('articles', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 2 : 1,
				'sort_dir' => 'asc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_articles; ++$i) {
				$articles[$i]['period'] = $this->injector['Date']->formatTimeRange($articles[$i]['start'], $articles[$i]['end']);
			}
			$this->injector['View']->assign('articles', $articles);
			$this->injector['View']->assign('can_delete', $can_delete);
		}
	}

}