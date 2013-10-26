<?php

namespace ACP3\Modules\Categories;

use ACP3\Core;

/**
 * Description of CategoriesAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller {

	public function __construct() {
		parent::__construct();
	}

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (!empty($_FILES['picture']['name'])) {
				$file['tmp_name'] = $_FILES['picture']['tmp_name'];
				$file['name'] = $_FILES['picture']['name'];
				$file['size'] = $_FILES['picture']['size'];
			}
			$settings = Core\Config::getSettings('categories');

			if (strlen($_POST['title']) < 3)
				$errors['title'] = $this->lang->t('categories', 'title_to_short');
			if (strlen($_POST['description']) < 3)
				$errors['description'] = $this->lang->t('categories', 'description_to_short');
			if (!empty($file) &&
					(empty($file['tmp_name']) ||
					empty($file['size']) ||
					Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
					$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
				$errors['picture'] = $this->lang->t('categories', 'invalid_image_selected');
			if (empty($_POST['module']))
				$errors['module'] = $this->lang->t('categories', 'select_module');
			if (strlen($_POST['title']) >= 3 && Helpers::categoriesCheckDuplicate($_POST['title'], $_POST['module']))
				$errors['title'] = $this->lang->t('categories', 'category_already_exists');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$file_sql = null;
				if (!empty($file)) {
					$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'categories');
					$file_sql = array('picture' => $result['name']);
				}

				$mod_id = $this->db->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['module']));
				$insert_values = array(
					'id' => '',
					'title' => Core\Functions::strEncode($_POST['title']),
					'description' => Core\Functions::strEncode($_POST['description']),
					'module_id' => $mod_id,
				);
				if (is_array($file_sql) === true) {
					$insert_values = array_merge($insert_values, $file_sql);
				}

				$bool = $this->db->insert(DB_PRE . 'categories', $insert_values);
				Helpers::setCategoriesCache($_POST['module']);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/categories');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$this->view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'description' => ''));

			$mod_list = Core\Modules::getActiveModules();
			foreach ($mod_list as $name => $info) {
				if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
					$mod_list[$name]['selected'] = Core\Functions::selectEntry('module', $info['dir']);
				} else {
					unset($mod_list[$name]);
				}
			}
			$this->view->assign('mod_list', $mod_list);

			$this->session->generateFormToken();
		}
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->uri->entries) === true)
			$entries = $this->uri->entries;

		if (!isset($entries)) {
			$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->view->setContent(Core\Functions::confirmBox($this->lang->t('system', 'confirm_delete'), $this->uri->route('acp/categories/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->uri->route('acp/categories')));
		} elseif ($this->uri->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$in_use = false;

			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($entry)) == 1) {
					$category = $this->db->fetchAssoc('SELECT c.picture, m.name AS module FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($entry));
					if ($this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . $category['module'] . ' WHERE category_id = ?', array($entry)) > 0) {
						$in_use = true;
					} else {
						// Kategoriebild ebenfalls löschen
						Core\Functions::removeUploadedFile('categories', $category['picture']);
						$bool = $this->db->delete(DB_PRE . 'categories', array('id' => $entry));
					}
				}
			}

			Core\Cache::purge('sql', 'categories');

			if ($in_use === true) {
				$text = $this->lang->t('categories', 'category_is_in_use');
				$bool = false;
			} else {
				$text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
			}
			Core\Functions::setRedirectMessage($bool, $text, 'acp/categories');
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($this->uri->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (!empty($_FILES['picture']['name'])) {
					$file['tmp_name'] = $_FILES['picture']['tmp_name'];
					$file['name'] = $_FILES['picture']['name'];
					$file['size'] = $_FILES['picture']['size'];
				}
				$settings = Core\Config::getSettings('categories');
				$module = $this->db->fetchAssoc('SELECT m.name FROM ' . DB_PRE . 'modules AS m JOIN ' . DB_PRE . 'categories AS c ON(m.id = c.module_id) WHERE c.id = ?', array($this->uri->id));

				if (strlen($_POST['title']) < 3)
					$errors['title'] = $this->lang->t('categories', 'title_to_short');
				if (strlen($_POST['description']) < 3)
					$errors['description'] = $this->lang->t('categories', 'description_to_short');
				if (!empty($file) &&
						(empty($file['tmp_name']) ||
						empty($file['size']) ||
						Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
						$_FILES['file']['error'] !== UPLOAD_ERR_OK))
					$errors['picture'] = $this->lang->t('categories', 'invalid_image_selected');
				if (strlen($_POST['title']) >= 3 && Helpers::categoriesCheckDuplicate($_POST['title'], $module['name'], $this->uri->id))
					$errors['title'] = $this->lang->t('categories', 'category_already_exists');

				if (isset($errors) === true) {
					$this->view->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
				} else {
					$new_file_sql = null;
					if (isset($file) && is_array($file)) {
						$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'categories');
						$new_file_sql['picture'] = $result['name'];
					}

					$update_values = array(
						'title' => Core\Functions::strEncode($_POST['title']),
						'description' => Core\Functions::strEncode($_POST['description']),
					);
					if (is_array($new_file_sql) === true) {
						$old_file = $this->db->fetchColumn('SELECT picture FROM ' . DB_PRE . 'categories WEHRE id = ?', array($this->uri->id));
						Core\Functions::removeUploadedFile('categories', $old_file);

						$update_values = array_merge($update_values, $new_file_sql);
					}

					$bool = $this->db->update(DB_PRE . 'categories', $update_values, array('id' => $this->uri->id));

					Helpers::setCategoriesCache($module['name']);

					$this->session->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$category = $this->db->fetchAssoc('SELECT title, description FROM ' . DB_PRE . 'categories WHERE id = ?', array($this->uri->id));

				$this->view->assign('form', isset($_POST['submit']) ? $_POST : $category);

				$this->session->generateFormToken();
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$categories = $this->db->fetchAll('SELECT c.id, c.title, c.description, m.name AS module FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) ORDER BY m.name ASC, c.title DESC, c.id DESC');
		$c_categories = count($categories);

		if ($c_categories > 0) {
			$can_delete = Core\Modules::hasPermission('categories', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->view->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_categories; ++$i) {
				$categories[$i]['module'] = $this->lang->t($categories[$i]['module'], $categories[$i]['module']);
			}
			$this->view->assign('categories', $categories);
			$this->view->assign('can_delete', $can_delete);
		}
	}

	public function actionSettings() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::isNumber($_POST['width']) === false)
				$errors['width'] = $this->lang->t('categories', 'invalid_image_width_entered');
			if (Core\Validate::isNumber($_POST['height']) === false)
				$errors['height'] = $this->lang->t('categories', 'invalid_image_height_entered');
			if (Core\Validate::isNumber($_POST['filesize']) === false)
				$errors['filesize'] = $this->lang->t('categories', 'invalid_image_filesize_entered');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'width' => (int) $_POST['width'],
					'height' => (int) $_POST['height'],
					'filesize' => (int) $_POST['filesize'],
				);
				$bool = Core\Config::setSettings('categories', $data);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/categories');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('categories');

			$this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->session->generateFormToken();
		}
	}

}