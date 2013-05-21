<?php

namespace ACP3\Modules\Emoticons;

use ACP3\Core;

/**
 * Description of EmoticonsAdmin
 *
 * @author Tino
 */
class EmoticonsAdmin extends Core\ModuleController {

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (!empty($_FILES['picture']['tmp_name'])) {
				$file['tmp_name'] = $_FILES['picture']['tmp_name'];
				$file['name'] = $_FILES['picture']['name'];
				$file['size'] = $_FILES['picture']['size'];
			}
			$settings = Core\Config::getSettings('emoticons');

			if (empty($_POST['code']))
				$errors['code'] = Core\Registry::get('Lang')->t('emoticons', 'type_in_code');
			if (empty($_POST['description']))
				$errors['description'] = Core\Registry::get('Lang')->t('emoticons', 'type_in_description');
			if (!isset($file) ||
					Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
					$_FILES['picture']['error'] !== UPLOAD_ERR_OK)
				$errors['picture'] = Core\Registry::get('Lang')->t('emoticons', 'invalid_image_selected');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'emoticons');

				$insert_values = array(
					'id' => '',
					'code' => Core\Functions::str_encode($_POST['code']),
					'description' => Core\Functions::str_encode($_POST['description']),
					'img' => $result['name'],
				);

				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'emoticons', $insert_values);
				EmoticonsFunctions::setEmoticonsCache();

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('code' => '', 'description' => ''));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/emoticons/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/emoticons')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($entry)) == 1) {
					// Datei ebenfalls löschen
					$file = Core\Registry::get('Db')->fetchColumn('SELECT img FROM ' . DB_PRE . 'emoticons WHERE id = ?', array($entry));
					Core\Functions::removeUploadedFile('emoticons', $file);
					$bool = Core\Registry::get('Db')->delete(DB_PRE . 'emoticons', array('id' => $entry));
				}
			}

			EmoticonsFunctions::setEmoticonsCache();

			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/emoticons');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'emoticons WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (!empty($_FILES['picture']['tmp_name'])) {
					$file['tmp_name'] = $_FILES['picture']['tmp_name'];
					$file['name'] = $_FILES['picture']['name'];
					$file['size'] = $_FILES['picture']['size'];
				}
				$settings = Core\Config::getSettings('emoticons');

				if (empty($_POST['code']))
					$errors['code'] = Core\Registry::get('Lang')->t('emoticons', 'type_in_code');
				if (empty($_POST['description']))
					$errors['description'] = Core\Registry::get('Lang')->t('emoticons', 'type_in_description');
				if (!empty($file['tmp_name']) &&
						(Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
						$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
					$errors['picture'] = Core\Registry::get('Lang')->t('emoticons', 'invalid_image_selected');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
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
						$old_file = Core\Registry::get('Db')->fetchColumn('SELECT img FROM emoticons WHERE id = ?', array(Core\Registry::get('URI')->id));
						Core\Functions::removeUploadedFile('emoticons', $old_file);

						$update_values = array_merge($update_values, $new_file_sql);
					}

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'emoticons', $update_values, array('id' => Core\Registry::get('URI')->id));
					EmoticonsFunctions::setEmoticonsCache();

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$emoticon = Core\Registry::get('Db')->fetchAssoc('SELECT code, description FROM ' . DB_PRE . 'emoticons WHERE id = ?', array(Core\Registry::get('URI')->id));

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $emoticon);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$emoticons = Core\Registry::get('Db')->fetchAll('SELECT id, code, description, img FROM ' . DB_PRE . 'emoticons ORDER BY id DESC');
		$c_emoticons = count($emoticons);

		if ($c_emoticons > 0) {
			$can_delete = Core\Modules::hasPermission('emoticons', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 4 : 3,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->assign('emoticons', $emoticons);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));
		}
	}

	public function actionSettings() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::isNumber($_POST['width']) === false)
				$errors['width'] = Core\Registry::get('Lang')->t('emoticons', 'invalid_image_width_entered');
			if (Core\Validate::isNumber($_POST['height']) === false)
				$errors['height'] = Core\Registry::get('Lang')->t('emoticons', 'invalid_image_height_entered');
			if (Core\Validate::isNumber($_POST['filesize']) === false)
				$errors['filesize'] = Core\Registry::get('Lang')->t('emoticons', 'invalid_image_filesize_entered');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'width' => (int) $_POST['width'],
					'height' => (int) $_POST['height'],
					'filesize' => (int) $_POST['filesize'],
				);
				$bool = Core\Config::setSettings('emoticons', $data);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('emoticons');

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

}