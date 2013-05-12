<?php

namespace ACP3\Modules\Emoticons;

use ACP3\Core;

/**
 * Description of EmoticonsAdmin
 *
 * @author Tino
 */
class EmoticonsAdmin extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
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
				$errors['code'] = $this->injector['Lang']->t('emoticons', 'type_in_code');
			if (empty($_POST['description']))
				$errors['description'] = $this->injector['Lang']->t('emoticons', 'type_in_description');
			if (!isset($file) ||
					Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
					$_FILES['picture']['error'] !== UPLOAD_ERR_OK)
				$errors['picture'] = $this->injector['Lang']->t('emoticons', 'invalid_image_selected');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');

				$insert_values = array(
					'id' => '',
					'code' => Core\Functions::str_encode($_POST['code']),
					'description' => Core\Functions::str_encode($_POST['description']),
					'img' => $result['name'],
				);

				$bool = $this->injector['Db']->insert(DB_PRE . 'emoticons', $insert_values);
				setEmoticonsCache();

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('code' => '', 'description' => ''));

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->injector['URI']->entries) === true)
			$entries = $this->injector['URI']->entries;

		if (!isset($entries)) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/emoticons/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/emoticons')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			require_once MODULES_DIR . 'emoticons/functions.php';

			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($entry)) == 1) {
					// Datei ebenfalls löschen
					$file = $this->injector['Db']->fetchColumn('SELECT img FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($entry));
					Core\Functions::removeUploadedFile('emoticons', $file);
					$bool = $this->injector['Db']->delete(DB_PRE . 'emoticons', array('id' => $entry));
				}
			}

			setEmoticonsCache();

			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/emoticons');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			if (isset($_POST['submit']) === true) {
				if (!empty($_FILES['picture']['tmp_name'])) {
					$file['tmp_name'] = $_FILES['picture']['tmp_name'];
					$file['name'] = $_FILES['picture']['name'];
					$file['size'] = $_FILES['picture']['size'];
				}
				$settings = Core\Config::getSettings('emoticons');

				if (empty($_POST['code']))
					$errors['code'] = $this->injector['Lang']->t('emoticons', 'type_in_code');
				if (empty($_POST['description']))
					$errors['description'] = $this->injector['Lang']->t('emoticons', 'type_in_description');
				if (!empty($file['tmp_name']) &&
						(Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
						$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
					$errors['picture'] = $this->injector['Lang']->t('emoticons', 'invalid_image_selected');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					$new_file_sql = null;
					if (isset($file)) {
						$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');
						$new_file_sql['img'] = $result['name'];
					}

					$update_values = array(
						'code' => Core\Functions::str_encode($_POST['code']),
						'description' => Core\Functions::str_encode($_POST['description']),
					);
					if (is_array($new_file_sql) === true) {
						$old_file = $this->injector['Db']->fetchColumn('SELECT img FROM emoticons WHERE id = ?', array($this->injector['URI']->id));
						Core\Functions::removeUploadedFile('emoticons', $old_file);

						$update_values = array_merge($update_values, $new_file_sql);
					}

					$bool = $this->injector['Db']->update(DB_PRE . 'emoticons', $update_values, array('id' => $this->injector['URI']->id));
					setEmoticonsCache();

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$emoticon = $this->injector['Db']->fetchAssoc('SELECT code, description FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($this->injector['URI']->id));

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $emoticon);

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$emoticons = $this->injector['Db']->fetchAll('SELECT id, code, description, img FROM ' . DB_PRE . 'emoticons ORDER BY id DESC');
		$c_emoticons = count($emoticons);

		if ($c_emoticons > 0) {
			$can_delete = Core\Modules::check('emoticons', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 4 : 3,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->assign('emoticons', $emoticons);
			$this->injector['View']->assign('can_delete', $can_delete);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));
		}
	}

	public function actionSettings() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::isNumber($_POST['width']) === false)
				$errors['width'] = $this->injector['Lang']->t('emoticons', 'invalid_image_width_entered');
			if (Core\Validate::isNumber($_POST['height']) === false)
				$errors['height'] = $this->injector['Lang']->t('emoticons', 'invalid_image_height_entered');
			if (Core\Validate::isNumber($_POST['filesize']) === false)
				$errors['filesize'] = $this->injector['Lang']->t('emoticons', 'invalid_image_filesize_entered');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'width' => (int) $_POST['width'],
					'height' => (int) $_POST['height'],
					'filesize' => (int) $_POST['filesize'],
				);
				$bool = Core\Config::setSettings('emoticons', $data);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('emoticons');

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->injector['Session']->generateFormToken();
		}
	}

}