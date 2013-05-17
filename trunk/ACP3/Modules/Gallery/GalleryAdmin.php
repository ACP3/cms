<?php

namespace ACP3\Modules\Gallery;

use ACP3\Core;

/**
 * Description of GalleryAdmin
 *
 * @author Tino
 */
class GalleryAdmin extends Core\ModuleController {

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = Core\Registry::get('Lang')->t('gallery', 'type_in_gallery_title');
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
					'user_id' => Core\Registry::get('Auth')->getUserId(),
				);

				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'gallery', $insert_values);
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
					Core\SEO::insertUriAlias('gallery/pics/id_' . Core\Registry::get('Db')->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/gallery');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Datumsauswahl
			Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end')));

			Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionCreate_picture() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$gallery = Core\Registry::get('Db')->fetchColumn('SELECT title FROM ' . DB_PRE . 'gallery WHERE id = ?', array(Core\Registry::get('URI')->id));

			Core\Registry::get('Breadcrumb')
					->append($gallery, Core\Registry::get('URI')->route('acp/gallery/edit/id_' . Core\Registry::get('URI')->id))
					->append(Core\Registry::get('Lang')->t('gallery', 'acp_create_picture'));

			$settings = Core\Config::getSettings('gallery');

			if (isset($_POST['submit']) === true) {
				$file['tmp_name'] = $_FILES['file']['tmp_name'];
				$file['name'] = $_FILES['file']['name'];
				$file['size'] = $_FILES['file']['size'];

				if (empty($file['tmp_name']))
					$errors['file'] = Core\Registry::get('Lang')->t('gallery', 'no_picture_selected');
				if (!empty($file['tmp_name']) &&
						(Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
						$_FILES['file']['error'] !== UPLOAD_ERR_OK))
					$errors['file'] = Core\Registry::get('Lang')->t('gallery', 'invalid_image_selected');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
					$picNum = Core\Registry::get('Db')->fetchColumn('SELECT MAX(pic) FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array(Core\Registry::get('URI')->id));

					$insert_values = array(
						'id' => '',
						'pic' => !is_null($picNum) ? $picNum + 1 : 1,
						'gallery_id' => Core\Registry::get('URI')->id,
						'file' => $result['name'],
						'description' => Core\Functions::str_encode($_POST['description'], true),
						'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
					);

					$bool = Core\Registry::get('Db')->insert(DB_PRE . 'gallery_pictures', $insert_values);
					$bool2 = GalleryFunctions::generatePictureAlias(Core\Registry::get('Db')->lastInsertId());
					GalleryFunctions::setGalleryCache(Core\Registry::get('URI')->id);

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool && $bool2, Core\Registry::get('Lang')->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/edit/id_' . Core\Registry::get('URI')->id);
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
					$options = array();
					$options[0]['name'] = 'comments';
					$options[0]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
					$options[0]['lang'] = Core\Registry::get('Lang')->t('system', 'allow_comments');
					Core\Registry::get('View')->assign('options', $options);
				}

				$galleries = Core\Registry::get('Db')->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'gallery ORDER BY start DESC');
				$c_galleries = count($galleries);
				for ($i = 0; $i < $c_galleries; ++$i) {
					$galleries[$i]['selected'] = Core\Functions::selectEntry('gallery', $galleries[$i]['id'], Core\Registry::get('URI')->id);
					$galleries[$i]['date'] = Core\Registry::get('Date')->format($galleries[$i]['start']);
				}

				Core\Registry::get('View')->assign('galleries', $galleries);
				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('description' => ''));
				Core\Registry::get('View')->assign('gallery_id', Core\Registry::get('URI')->id);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
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
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/gallery/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/gallery')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = $bool2 = false;

			foreach ($marked_entries as $entry) {
				if (!empty($entry) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($entry)) == 1) {
					// Hochgeladene Bilder löschen
					$pictures = Core\Registry::get('Db')->fetchAll('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($entry));
					foreach ($pictures as $row) {
						removePicture($row['file']);
					}
					// Galerie Cache löschen
					Core\Cache::delete('pics_id_' . $entry, 'gallery');
					Core\SEO::deleteUriAlias('gallery/pics/id_' . $entry);
					GalleryFunctions::deletePictureAliases($entry);

					// Fotogalerie mitsamt Bildern löschen
					$bool = Core\Registry::get('Db')->delete(DB_PRE . 'gallery', array('id' => $entry));
					$bool2 = Core\Registry::get('Db')->delete(DB_PRE . 'gallery_pictures', array('gallery_id' => $entry));
				}
			}
			Core\Functions::setRedirectMessage($bool && $bool2, Core\Registry::get('Lang')->t('system', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/gallery');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionDelete_picture() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/gallery/delete_picture/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/gallery/edit/id_' . Core\Registry::get('URI')->id)));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry)) == 1) {
					// Datei ebenfalls löschen
					$picture = Core\Registry::get('Db')->fetchAssoc('SELECT pic, gallery_id, file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry));
					Core\Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'gallery_pictures SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', array($picture['pic'], $picture['gallery_id']));
					GalleryFunctions::removePicture($picture['file']);

					$bool = Core\Registry::get('Db')->delete(DB_PRE . 'gallery_pictures', array('id' => $entry));
					Core\SEO::deleteUriAlias('gallery/details/id_' . $entry);
					GalleryFunctions::setGalleryCache($picture['gallery_id']);
				}
			}
			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$gallery = Core\Registry::get('Db')->fetchAssoc('SELECT start, end, title FROM ' . DB_PRE . 'gallery WHERE id = ?', array(Core\Registry::get('URI')->id));

			Core\Registry::get('View')->assign('SEO_FORM_FIELDS', Core\SEO::formFields('gallery/pics/id_' . Core\Registry::get('URI')->id));

			Core\Registry::get('Breadcrumb')->append($gallery['title']);

			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = Core\Registry::get('Lang')->t('gallery', 'type_in_gallery_title');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'gallery/pics/id_' . Core\Registry::get('URI')->id)))
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
						'user_id' => Core\Registry::get('Auth')->getUserId(),
					);

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'gallery', $update_values, array('id' => Core\Registry::get('URI')->id));
					if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
						Core\SEO::insertUriAlias('gallery/pics/id_' . Core\Registry::get('URI')->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
						GalleryFunctions::generatePictureAliases(Core\Registry::get('URI')->id);
					}

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
				}
			}
			if (isset($_POST['entries']) === false && isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				Core\Functions::getRedirectMessage();

				Core\Registry::get('View')->assign('gallery_id', Core\Registry::get('URI')->id);

				// Datumsauswahl
				Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end'), array($gallery['start'], $gallery['end'])));

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $gallery);

				$pictures = Core\Registry::get('Db')->fetchAll('SELECT id, pic, file, description FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ? ORDER BY pic ASC', array(Core\Registry::get('URI')->id));
				$c_pictures = count($pictures);

				if ($c_pictures > 0) {
					$can_delete = Core\Modules::check('gallery', 'acp_delete_picture');
					$config = array(
						'element' => '#acp-table',
						'hide_col_sort' => $can_delete === true ? 0 : ''
					);
					Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));

					for ($i = 0; $i < $c_pictures; ++$i) {
						$pictures[$i]['first'] = $i == 0 ? true : false;
						$pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
					}
					Core\Registry::get('View')->assign('pictures', $pictures);
					Core\Registry::get('View')->assign('can_delete', $can_delete);
					Core\Registry::get('View')->assign('can_order', Core\Modules::check('gallery', 'acp_order'));
					Core\Registry::get('View')->assign('can_edit_picture', Core\Modules::check('gallery', 'acp_edit_picture'));
				}

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit_picture() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$picture = Core\Registry::get('Db')->fetchAssoc('SELECT p.gallery_id, p.file, p.description, p.comments, g.title AS gallery_title FROM ' . DB_PRE . 'gallery_pictures AS p, ' . DB_PRE . 'gallery AS g WHERE p.id = ? AND p.gallery_id = g.id', array(Core\Registry::get('URI')->id));

			Core\Registry::get('Breadcrumb')
					->append($picture['gallery_title'], Core\Registry::get('URI')->route('acp/gallery/edit/id_' . $picture['gallery_id']))
					->append(Core\Registry::get('Lang')->t('gallery', 'acp_edit_picture'));

			$settings = Core\Config::getSettings('gallery');

			if (isset($_POST['submit']) === true) {
				if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
					$file['tmp_name'] = $_FILES['file']['tmp_name'];
					$file['name'] = $_FILES['file']['name'];
					$file['size'] = $_FILES['file']['size'];
				}

				if (!empty($file['tmp_name']) &&
						(Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
						$_FILES['file']['error'] !== UPLOAD_ERR_OK))
					$errors['file'] = Core\Registry::get('Lang')->t('gallery', 'invalid_image_selected');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$new_file_sql = null;
					if (isset($file) && is_array($file)) {
						$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
						$new_file_sql['file'] = $result['name'];
					}

					$update_values = array(
						'description' => Core\Functions::str_encode($_POST['description'], true),
						'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
					);
					if (is_array($new_file_sql) === true) {
						$old_file = Core\Registry::get('Db')->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(Core\Registry::get('URI')->id));
						GalleryFunctions::removePicture($old_file);

						$update_values = array_merge($update_values, $new_file_sql);
					}

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'gallery_pictures', $update_values, array('id' => Core\Registry::get('URI')->id));
					GalleryFunctions::setGalleryCache($picture['gallery_id']);

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
					$options = array();
					$options[0]['name'] = 'comments';
					$options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $picture['comments'], 'checked');
					$options[0]['lang'] = Core\Registry::get('Lang')->t('system', 'allow_comments');
					Core\Registry::get('View')->assign('options', $options);
				}

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $picture);
				Core\Registry::get('View')->assign('gallery_id', Core\Registry::get('URI')->id);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$galleries = Core\Registry::get('Db')->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . DB_PRE . 'gallery AS g LEFT JOIN ' . DB_PRE . 'gallery_pictures AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
		$c_galleries = count($galleries);

		if ($c_galleries > 0) {
			$can_delete = Core\Modules::check('gallery', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_galleries; ++$i) {
				$galleries[$i]['period'] = Core\Registry::get('Date')->formatTimeRange($galleries[$i]['start'], $galleries[$i]['end']);
			}
			Core\Registry::get('View')->assign('galleries', $galleries);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

	public function actionOrder() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true) {
			if ((Core\Registry::get('URI')->action === 'up' || Core\Registry::get('URI')->action === 'down') &&
					Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
				Core\Functions::moveOneStep(Core\Registry::get('URI')->action, 'gallery_pictures', 'id', 'pic', Core\Registry::get('URI')->id, 'gallery_id');

				$gallery_id = Core\Registry::get('Db')->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array(Core\Registry::get('URI')->id));

				GalleryFunctions::setGalleryCache($gallery_id);

				Core\Registry::get('URI')->redirect('acp/gallery/edit/id_' . $gallery_id);
			}
		}
		Core\Registry::get('URI')->redirect('errors/404');
	}

	public function actionSettings() {
		$settings = Core\Config::getSettings('gallery');
		$comments_active = Core\Modules::isActive('comments');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = Core\Registry::get('Lang')->t('system', 'select_date_format');
			if (Core\Validate::isNumber($_POST['sidebar']) === false)
				$errors['sidebar'] = Core\Registry::get('Lang')->t('system', 'select_sidebar_entries');
			if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
				$errors[] = Core\Registry::get('Lang')->t('gallery', 'select_use_overlay');
			if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
				$errors[] = Core\Registry::get('Lang')->t('gallery', 'select_allow_comments');
			if (Core\Validate::isNumber($_POST['thumbwidth']) === false || Core\Validate::isNumber($_POST['width']) === false || Core\Validate::isNumber($_POST['maxwidth']) === false)
				$errors[] = Core\Registry::get('Lang')->t('gallery', 'invalid_image_width_entered');
			if (Core\Validate::isNumber($_POST['thumbheight']) === false || Core\Validate::isNumber($_POST['height']) === false || Core\Validate::isNumber($_POST['maxheight']) === false)
				$errors[] = Core\Registry::get('Lang')->t('gallery', 'invalid_image_height_entered');
			if (Core\Validate::isNumber($_POST['filesize']) === false)
				$errors['filesize'] = Core\Registry::get('Lang')->t('gallery', 'invalid_image_filesize_entered');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'width' => (int) $_POST['width'],
					'height' => (int) $_POST['height'],
					'thumbwidth' => (int) $_POST['thumbwidth'],
					'thumbheight' => (int) $_POST['thumbheight'],
					'maxwidth' => (int) $_POST['maxwidth'],
					'maxheight' => (int) $_POST['maxheight'],
					'filesize' => (int) $_POST['filesize'],
					'overlay' => $_POST['overlay'],
					'dateformat' => Core\Functions::str_encode($_POST['dateformat']),
					'sidebar' => (int) $_POST['sidebar'],
				);
				if ($comments_active === true)
					$data['comments'] = $_POST['comments'];

				$bool = Core\Config::setSettings('gallery', $data);

				// Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
				if ($_POST['thumbwidth'] !== $settings['thumbwidth'] || $_POST['thumbheight'] !== $settings['thumbheight'] ||
						$_POST['width'] !== $settings['width'] || $_POST['height'] !== $settings['height']) {
					Core\Cache::purge('images', 'gallery');
					Core\Cache::purge('sql', 'gallery');
				}

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/gallery');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			if ($comments_active === true) {
				$lang_comments = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
			}

			$lang_overlay = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

			Core\Registry::get('View')->assign('dateformat', Core\Registry::get('Date')->dateformatDropdown($settings['dateformat']));

			Core\Registry::get('View')->assign('sidebar_entries', Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

}