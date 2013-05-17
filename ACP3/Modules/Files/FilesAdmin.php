<?php

namespace ACP3\Modules\Files;

use ACP3\Core;
use ACP3\Modules\Categories\CategoriesFunctions;

/**
 * Description of FilesAdmin
 *
 * @author Tino
 */
class FilesAdmin extends Core\ModuleController {

	public function actionCreate() {
		$settings = Core\Config::getSettings('files');

		if (isset($_POST['submit']) === true) {
			if (isset($_POST['external'])) {
				$file = $_POST['file_external'];
			} else {
				$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
				$file['name'] = $_FILES['file_internal']['name'];
				$file['size'] = $_FILES['file_internal']['size'];
			}

			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['link-title'] = Core\Registry::get('Lang')->t('files', 'type_in_title');
			if (isset($_POST['external']) && (empty($file) || empty($_POST['filesize']) || empty($_POST['unit'])))
				$errors['external'] = Core\Registry::get('Lang')->t('files', 'type_in_external_resource');
			if (!isset($_POST['external']) &&
					(empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK))
				$errors['file-internal'] = Core\Registry::get('Lang')->t('files', 'select_internal_resource');
			if (strlen($_POST['text']) < 3)
				$errors['text'] = Core\Registry::get('Lang')->t('files', 'description_to_short');
			if (strlen($_POST['cat_create']) < 3 && CategoriesFunctions::categoriesCheck($_POST['cat']) === false)
				$errors['cat'] = Core\Registry::get('Lang')->t('files', 'select_category');
			if (strlen($_POST['cat_create']) >= 3 && CategoriesFunctions::categoriesCheckDuplicate($_POST['cat_create'], 'files') === true)
				$errors['cat-create'] = Core\Registry::get('Lang')->t('categories', 'category_already_exists');
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) && (Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias']) === true))
				$errors['alias'] = Core\Registry::get('Lang')->t('system', 'uri_alias_unallowed_characters_or_exists');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				if (is_array($file) === true) {
					$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'files');
					$new_file = $result['name'];
					$filesize = $result['size'];
				} else {
					$_POST['filesize'] = (float) $_POST['filesize'];
					$new_file = $file;
					$filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
				}

				$insert_values = array(
					'id' => '',
					'start' => Core\Registry::get('Date')->toSQL($_POST['start']),
					'end' => Core\Registry::get('Date')->toSQL($_POST['end']),
					'category_id' => strlen($_POST['cat_create']) >= 3 ? CategoriesFunctions::categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
					'file' => $new_file,
					'size' => $filesize,
					'title' => Core\Functions::str_encode($_POST['title']),
					'text' => Core\Functions::str_encode($_POST['text'], true),
					'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
					'user_id' => Core\Registry::get('Auth')->getUserId(),
				);


				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'files', $insert_values);
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
					Core\SEO::insertUriAlias('files/details/id_' . Core\Registry::get('Db')->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/files');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Datumsauswahl
			Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end')));

			$units = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB');
			Core\Registry::get('View')->assign('units', Core\Functions::selectGenerator('units', $units, $units, ''));

			// Formularelemente
			Core\Registry::get('View')->assign('categories', CategoriesFunctions::categoriesList('files', '', true));

			if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
				$options = array();
				$options[0]['name'] = 'comments';
				$options[0]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
				$options[0]['lang'] = Core\Registry::get('Lang')->t('system', 'allow_comments');
				Core\Registry::get('View')->assign('options', $options);
			}

			Core\Registry::get('View')->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');

			$defaults = array(
				'title' => '',
				'file_internal' => '',
				'file_external' => '',
				'filesize' => '',
				'text' => '',
				'alias' => '',
				'seo_keywords' => '',
				'seo_description' => '',
			);

			Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

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
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/files/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/files')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$commentsInstalled = Core\Modules::isInstalled('comments');
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = ?', array($entry)) == 1) {
					// Datei ebenfalls löschen
					$file = Core\Registry::get('Db')->fetchColumn('SELECT file FROM ' . DB_PRE . 'files WHERE id = ?', array($entry));
					Core\Functions::removeUploadedFile('files', $file);
					$bool = Core\Registry::get('Db')->delete(DB_PRE . 'files', array('id' => $entry));
					if ($commentsInstalled === true)
						Core\Registry::get('Db')->delete(DB_PRE . 'comments', array('module' => 'files', 'entry_id' => $entry));

					Core\Cache::delete('details_id_' . $entry, 'files');
					Core\SEO::deleteUriAlias('files/details/id_' . $entry);
				}
			}
			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/files');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$settings = Core\Config::getSettings('files');

			if (isset($_POST['submit']) === true) {
				if (isset($_POST['external'])) {
					$file = $_POST['file_external'];
				} elseif (!empty($_FILES['file_internal']['name'])) {
					$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
					$file['name'] = $_FILES['file_internal']['name'];
					$file['size'] = $_FILES['file_internal']['size'];
				}

				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['link-title'] = Core\Registry::get('Lang')->t('files', 'type_in_title');
				if (isset($_POST['external']) && (empty($file) || empty($_POST['filesize']) || empty($_POST['unit'])))
					$errors['external'] = Core\Registry::get('Lang')->t('files', 'type_in_external_resource');
				if (!isset($_POST['external']) && isset($file) && is_array($file) &&
						(empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK))
					$errors['file-internal'] = Core\Registry::get('Lang')->t('files', 'select_internal_resource');
				if (strlen($_POST['text']) < 3)
					$errors['text'] = Core\Registry::get('Lang')->t('files', 'description_to_short');
				if (strlen($_POST['cat_create']) < 3 && CategoriesFunctions::categoriesCheck($_POST['cat']) === false)
					$errors['cat'] = Core\Registry::get('Lang')->t('files', 'select_category');
				if (strlen($_POST['cat_create']) >= 3 && CategoriesFunctions::categoriesCheckDuplicate($_POST['cat_create'], 'files') === true)
					$errors['cat-create'] = Core\Registry::get('Lang')->t('categories', 'category_already_exists');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'files/details/id_' . Core\Registry::get('URI')->id) === true))
					$errors['alias'] = Core\Registry::get('Lang')->t('system', 'uri_alias_unallowed_characters_or_exists');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$new_file_sql = null;
					// Falls eine neue Datei angegeben wurde, Änderungen durchführen
					if (isset($file)) {
						if (is_array($file) === true) {
							$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'files');
							$new_file = $result['name'];
							$filesize = $result['size'];
						} else {
							$_POST['filesize'] = (float) $_POST['filesize'];
							$new_file = $file;
							$filesize = $_POST['filesize'] . ' ' . $_POST['unit'];
						}
						// SQL Query für die Änderungen
						$new_file_sql = array(
							'file' => $new_file,
							'size' => $filesize,
						);
					}

					$update_values = array(
						'start' => Core\Registry::get('Date')->toSQL($_POST['start']),
						'end' => Core\Registry::get('Date')->toSQL($_POST['end']),
						'category_id' => strlen($_POST['cat_create']) >= 3 ? CategoriesFunctions::categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
						'title' => Core\Functions::str_encode($_POST['title']),
						'text' => Core\Functions::str_encode($_POST['text'], true),
						'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
						'user_id' => Core\Registry::get('Auth')->getUserId(),
					);
					if (is_array($new_file_sql) === true) {
						$old_file = Core\Registry::get('Db')->fetchColumn('SELECT file FROM ' . DB_PRE . 'files WHERE id = ?', array(Core\Registry::get('URI')->id));
						Core\Functions::removeUploadedFile('files', $old_file);

						$update_values = array_merge($update_values, $new_file_sql);
					}

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'files', $update_values, array('id' => Core\Registry::get('URI')->id));
					if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
						Core\SEO::insertUriAlias('files/details/id_' . Core\Registry::get('URI')->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

					FilesFunctions::setFilesCache(Core\Registry::get('URI')->id);

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/files');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$dl = Core\Registry::get('Db')->fetchAssoc('SELECT start, end, category_id, file, size, title, text, comments FROM ' . DB_PRE . 'files WHERE id = ?', array(Core\Registry::get('URI')->id));

				// Datumsauswahl
				Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end'), array($dl['start'], $dl['end'])));

				$units = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB');
				Core\Registry::get('View')->assign('units', Core\Functions::selectGenerator('units', $units, $units, trim(strrchr($dl['size'], ' '))));

				$dl['filesize'] = substr($dl['size'], 0, strpos($dl['size'], ' '));

				// Formularelemente
				Core\Registry::get('View')->assign('categories', CategoriesFunctions::categoriesList('files', $dl['category_id'], true));

				if ($settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
					$options = array();
					$options[0]['name'] = 'comments';
					$options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $dl['comments'], 'checked');
					$options[0]['lang'] = Core\Registry::get('Lang')->t('system', 'allow_comments');
					Core\Registry::get('View')->assign('options', $options);
				}

				Core\Registry::get('View')->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');
				Core\Registry::get('View')->assign('current_file', $dl['file']);

				Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields('files/details/id_' . Core\Registry::get('URI')->id));
				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $dl);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/403');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$files = Core\Registry::get('Db')->fetchAll('SELECT id, start, end, file, size, title FROM ' . DB_PRE . 'files ORDER BY start DESC, end DESC, id DESC');
		$c_files = count($files);

		if ($c_files > 0) {
			$can_delete = Core\Modules::check('files', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_files; ++$i) {
				$files[$i]['period'] = Core\Registry::get('Date')->formatTimeRange($files[$i]['start'], $files[$i]['end']);
				$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : Core\Registry::get('Lang')->t('files', 'unknown_filesize');
			}
			Core\Registry::get('View')->assign('files', $files);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

	public function actionSettings() {
		$comments_active = Core\Modules::isActive('comments');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = Core\Registry::get('Lang')->t('system', 'select_date_format');
			if (Core\Validate::isNumber($_POST['sidebar']) === false)
				$errors['sidebar'] = Core\Registry::get('Lang')->t('system', 'select_sidebar_entries');
			if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
				$errors[] = Core\Registry::get('Lang')->t('files', 'select_allow_comments');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'dateformat' => Core\Functions::str_encode($_POST['dateformat']),
					'sidebar' => (int) $_POST['sidebar'],
					'comments' => $_POST['comments']
				);
				$bool = Core\Config::setSettings('files', $data);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/files');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('files');

			if ($comments_active === true) {
				$lang_comments = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
			}

			Core\Registry::get('View')->assign('dateformat', Core\Registry::get('Date')->dateformatDropdown($settings['dateformat']));

			Core\Registry::get('View')->assign('sidebar_entries', Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

}