<?php

namespace ACP3\Modules\Emoticons;

use ACP3\Core;

/**
 * Description of EmoticonsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\AdminController {

	public function __construct() {
		parent::__construct();
	}

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (!empty($_FILES['picture']['tmp_name'])) {
				$file['tmp_name'] = $_FILES['picture']['tmp_name'];
				$file['name'] = $_FILES['picture']['name'];
				$file['size'] = $_FILES['picture']['size'];
			}
			$settings = Core\Config::getSettings('emoticons');

			if (empty($_POST['code']))
				$errors['code'] = $this->lang->t('emoticons', 'type_in_code');
			if (empty($_POST['description']))
				$errors['description'] = $this->lang->t('emoticons', 'type_in_description');
			if (!isset($file) ||
					Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
					$_FILES['picture']['error'] !== UPLOAD_ERR_OK)
				$errors['picture'] = $this->lang->t('emoticons', 'invalid_image_selected');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');

				$insert_values = array(
					'id' => '',
					'code' => Core\Functions::strEncode($_POST['code']),
					'description' => Core\Functions::strEncode($_POST['description']),
					'img' => $result['name'],
				);

				$bool = $this->db->insert(DB_PRE . 'emoticons', $insert_values);
				Helpers::setEmoticonsCache();

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$this->view->assign('form', isset($_POST['submit']) ? $_POST : array('code' => '', 'description' => ''));

			$this->session->generateFormToken();
		}
	}

	public function actionDelete() {
		$items = $this->_deleteItem('acp/emoticons/delete', 'acp/emoticons');
		
		if ($this->uri->action === 'confirmed') {
			$items = explode('|', $items);
			$bool = false;
			foreach ($items as $item) {
				if (!empty($item) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($item)) == 1) {
					// Datei ebenfalls löschen
					$file = $this->db->fetchColumn('SELECT img FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($item));
					Core\Functions::removeUploadedFile('emoticons', $file);
					$bool = $this->db->delete(DB_PRE . 'emoticons', array('id' => $item));
				}
			}

			Helpers::setEmoticonsCache();

			Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/emoticons');
		} elseif (is_string($items)) {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($this->uri->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (!empty($_FILES['picture']['tmp_name'])) {
					$file['tmp_name'] = $_FILES['picture']['tmp_name'];
					$file['name'] = $_FILES['picture']['name'];
					$file['size'] = $_FILES['picture']['size'];
				}
				$settings = Core\Config::getSettings('emoticons');

				if (empty($_POST['code']))
					$errors['code'] = $this->lang->t('emoticons', 'type_in_code');
				if (empty($_POST['description']))
					$errors['description'] = $this->lang->t('emoticons', 'type_in_description');
				if (!empty($file['tmp_name']) &&
						(Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
						$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
					$errors['picture'] = $this->lang->t('emoticons', 'invalid_image_selected');

				if (isset($errors) === true) {
					$this->view->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
				} else {
					$new_file_sql = null;
					if (isset($file)) {
						$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');
						$new_file_sql['img'] = $result['name'];
					}

					$update_values = array(
						'code' => Core\Functions::strEncode($_POST['code']),
						'description' => Core\Functions::strEncode($_POST['description']),
					);
					if (is_array($new_file_sql) === true) {
						$old_file = $this->db->fetchColumn('SELECT img FROM emoticons WHERE id = ?', array($this->uri->id));
						Core\Functions::removeUploadedFile('emoticons', $old_file);

						$update_values = array_merge($update_values, $new_file_sql);
					}

					$bool = $this->db->update(DB_PRE . 'emoticons', $update_values, array('id' => $this->uri->id));
					Helpers::setEmoticonsCache();

					$this->session->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$emoticon = $this->db->fetchAssoc('SELECT code, description FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($this->uri->id));

				$this->view->assign('form', isset($_POST['submit']) ? $_POST : $emoticon);

				$this->session->generateFormToken();
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$emoticons = $this->db->fetchAll('SELECT id, code, description, img FROM ' . DB_PRE . 'emoticons ORDER BY id DESC');
		$c_emoticons = count($emoticons);

		if ($c_emoticons > 0) {
			$can_delete = Core\Modules::hasPermission('emoticons', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 4 : 3,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->view->assign('emoticons', $emoticons);
			$this->view->assign('can_delete', $can_delete);
			$this->view->appendContent(Core\Functions::datatable($config));
		}
	}

	public function actionSettings() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::isNumber($_POST['width']) === false)
				$errors['width'] = $this->lang->t('emoticons', 'invalid_image_width_entered');
			if (Core\Validate::isNumber($_POST['height']) === false)
				$errors['height'] = $this->lang->t('emoticons', 'invalid_image_height_entered');
			if (Core\Validate::isNumber($_POST['filesize']) === false)
				$errors['filesize'] = $this->lang->t('emoticons', 'invalid_image_filesize_entered');

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
				$bool = Core\Config::setSettings('emoticons', $data);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('emoticons');

			$this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->session->generateFormToken();
		}
	}

}
