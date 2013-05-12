<?php

namespace ACP3\Modules\Gallery;

use ACP3\Core;

/**
 * Description of GalleryAdmin
 *
 * @author Tino
 */
class GalleryAdmin extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_date');
			if (strlen($_POST['title']) < 3)
				$errors['title'] = $this->injector['Lang']->t('gallery', 'type_in_gallery_title');
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
					'user_id' => $this->injector['Auth']->getUserId(),
				);

				$bool = $this->injector['Db']->insert(DB_PRE . 'gallery', $insert_values);
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
					Core\SEO::insertUriAlias('gallery/pics/id_' . $this->injector['Db']->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/gallery');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Datumsauswahl
			$this->injector['View']->assign('publication_period', $this->injector['Date']->datepicker(array('start', 'end')));

			$this->injector['View']->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionCreate_picture() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$gallery = $this->injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->injector['URI']->id));

			$this->injector['Breadcrumb']
					->append($gallery, $this->injector['URI']->route('acp/gallery/edit/id_' . $this->injector['URI']->id))
					->append($this->injector['Lang']->t('gallery', 'acp_create_picture'));

			$settings = Core\Config::getSettings('gallery');

			if (isset($_POST['submit']) === true) {
				$file['tmp_name'] = $_FILES['file']['tmp_name'];
				$file['name'] = $_FILES['file']['name'];
				$file['size'] = $_FILES['file']['size'];

				if (empty($file['tmp_name']))
					$errors['file'] = $this->injector['Lang']->t('gallery', 'no_picture_selected');
				if (!empty($file['tmp_name']) &&
						(Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
						$_FILES['file']['error'] !== UPLOAD_ERR_OK))
					$errors['file'] = $this->injector['Lang']->t('gallery', 'invalid_image_selected');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
					$picNum = $this->injector['Db']->fetchColumn('SELECT MAX(pic) FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($this->injector['URI']->id));

					$insert_values = array(
						'id' => '',
						'pic' => !is_null($picNum) ? $picNum + 1 : 1,
						'gallery_id' => $this->injector['URI']->id,
						'file' => $result['name'],
						'description' => Core\Functions::str_encode($_POST['description'], true),
						'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
					);

					$bool = $this->injector['Db']->insert(DB_PRE . 'gallery_pictures', $insert_values);
					$bool2 = GalleryFunctions::generatePictureAlias($this->injector['Db']->lastInsertId());
					GalleryFunctions::setGalleryCache($this->injector['URI']->id);

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool && $bool2, $this->injector['Lang']->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/edit/id_' . $this->injector['URI']->id);
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::check('comments', 'functions') === true) {
					$options = array();
					$options[0]['name'] = 'comments';
					$options[0]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
					$options[0]['lang'] = $this->injector['Lang']->t('system', 'allow_comments');
					$this->injector['View']->assign('options', $options);
				}

				$galleries = $this->injector['Db']->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'gallery ORDER BY start DESC');
				$c_galleries = count($galleries);
				for ($i = 0; $i < $c_galleries; ++$i) {
					$galleries[$i]['selected'] = Core\Functions::selectEntry('gallery', $galleries[$i]['id'], $this->injector['URI']->id);
					$galleries[$i]['date'] = $this->injector['Date']->format($galleries[$i]['start']);
				}

				$this->injector['View']->assign('galleries', $galleries);
				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('description' => ''));
				$this->injector['View']->assign('gallery_id', $this->injector['URI']->id);

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
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
			$this->injector['View']->setContent(confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/gallery/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/gallery')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = $bool2 = false;

			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($entry)) == 1) {
					// Hochgeladene Bilder löschen
					$pictures = $this->injector['Db']->fetchAll('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($entry));
					foreach ($pictures as $row) {
						removePicture($row['file']);
					}
					// Galerie Cache löschen
					Core\Cache::delete('pics_id_' . $entry, 'gallery');
					Core\SEO::deleteUriAlias('gallery/pics/id_' . $entry);
					GalleryFunctions::deletePictureAliases($entry);

					// Fotogalerie mitsamt Bildern löschen
					$bool = $this->injector['Db']->delete(DB_PRE . 'gallery', array('id' => $entry));
					$bool2 = $this->injector['Db']->delete(DB_PRE . 'gallery_pictures', array('gallery_id' => $entry));
				}
			}
			Core\Functions::setRedirectMessage($bool && $bool2, $this->injector['Lang']->t('system', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/gallery');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionDelete_picture() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->injector['URI']->entries) === true)
			$entries = $this->injector['URI']->entries;

		if (!isset($entries)) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->injector['View']->setContent(confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/gallery/delete_picture/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/gallery/edit/id_' . $this->injector['URI']->id)));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry)) == 1) {
					// Datei ebenfalls löschen
					$picture = $this->injector['Db']->fetchAssoc('SELECT pic, gallery_id, file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry));
					$this->injector['Db']->executeUpdate('UPDATE ' . DB_PRE . 'gallery_pictures SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', array($picture['pic'], $picture['gallery_id']));
					GalleryFunctions::removePicture($picture['file']);

					$bool = $this->injector['Db']->delete(DB_PRE . 'gallery_pictures', array('id' => $entry));
					Core\SEO::deleteUriAlias('gallery/details/id_' . $entry);
					GalleryFunctions::setGalleryCache($picture['gallery_id']);
				}
			}
			Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$gallery = $this->injector['Db']->fetchAssoc('SELECT start, end, title FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->injector['URI']->id));

			$this->injector['View']->assign('SEO_FORM_FIELDS', Core\SEO::formFields('gallery/pics/id_' . $this->injector['URI']->id));

			$this->injector['Breadcrumb']->append($gallery['title']);

			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = $this->injector['Lang']->t('system', 'select_date');
				if (strlen($_POST['title']) < 3)
					$errors['title'] = $this->injector['Lang']->t('gallery', 'type_in_gallery_title');
				if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
						(Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'gallery/pics/id_' . $this->injector['URI']->id)))
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
						'user_id' => $this->injector['Auth']->getUserId(),
					);

					$bool = $this->injector['Db']->update(DB_PRE . 'gallery', $update_values, array('id' => $this->injector['URI']->id));
					if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
						Core\SEO::insertUriAlias('gallery/pics/id_' . $this->injector['URI']->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
						GalleryFunctions::generatePictureAliases($this->injector['URI']->id);
					}

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
				}
			}
			if (isset($_POST['entries']) === false && isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				Core\Functions::getRedirectMessage();

				$this->injector['View']->assign('gallery_id', $this->injector['URI']->id);

				// Datumsauswahl
				$this->injector['View']->assign('publication_period', $this->injector['Date']->datepicker(array('start', 'end'), array($gallery['start'], $gallery['end'])));

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $gallery);

				$pictures = $this->injector['Db']->fetchAll('SELECT id, pic, file, description FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ? ORDER BY pic ASC', array($this->injector['URI']->id));
				$c_pictures = count($pictures);

				if ($c_pictures > 0) {
					$can_delete = Core\Modules::check('gallery', 'acp_delete_picture');
					$config = array(
						'element' => '#acp-table',
						'hide_col_sort' => $can_delete === true ? 0 : ''
					);
					$this->injector['View']->appendContent(Core\Functions::datatable($config));

					for ($i = 0; $i < $c_pictures; ++$i) {
						$pictures[$i]['first'] = $i == 0 ? true : false;
						$pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
					}
					$this->injector['View']->assign('pictures', $pictures);
					$this->injector['View']->assign('can_delete', $can_delete);
					$this->injector['View']->assign('can_order', Core\Modules::check('gallery', 'acp_order'));
					$this->injector['View']->assign('can_edit_picture', Core\Modules::check('gallery', 'acp_edit_picture'));
				}

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit_picture() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$picture = $this->injector['Db']->fetchAssoc('SELECT p.gallery_id, p.file, p.description, p.comments, g.title AS gallery_title FROM ' . DB_PRE . 'gallery_pictures AS p, ' . DB_PRE . 'gallery AS g WHERE p.id = ? AND p.gallery_id = g.id', array($this->injector['URI']->id));

			$this->injector['Breadcrumb']
					->append($picture['gallery_title'], $this->injector['URI']->route('acp/gallery/edit/id_' . $picture['gallery_id']))
					->append($this->injector['Lang']->t('gallery', 'acp_edit_picture'));

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
					$errors['file'] = $this->injector['Lang']->t('gallery', 'invalid_image_selected');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
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
						$old_file = $this->injector['Db']->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($this->injector['URI']->id));
						GalleryFunctions::removePicture($old_file);

						$update_values = array_merge($update_values, $new_file_sql);
					}

					$bool = $this->injector['Db']->update(DB_PRE . 'gallery_pictures', $update_values, array('id' => $this->injector['URI']->id));
					GalleryFunctions::setGalleryCache($picture['gallery_id']);

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::check('comments', 'functions') === true) {
					$options = array();
					$options[0]['name'] = 'comments';
					$options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $picture['comments'], 'checked');
					$options[0]['lang'] = $this->injector['Lang']->t('system', 'allow_comments');
					$this->injector['View']->assign('options', $options);
				}

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $picture);
				$this->injector['View']->assign('gallery_id', $this->injector['URI']->id);

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$galleries = $this->injector['Db']->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . DB_PRE . 'gallery AS g LEFT JOIN ' . DB_PRE . 'gallery_pictures AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
		$c_galleries = count($galleries);

		if ($c_galleries > 0) {
			$can_delete = Core\Modules::check('gallery', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));
			for ($i = 0; $i < $c_galleries; ++$i) {
				$galleries[$i]['period'] = $this->injector['Date']->formatTimeRange($galleries[$i]['start'], $galleries[$i]['end']);
			}
			$this->injector['View']->assign('galleries', $galleries);
			$this->injector['View']->assign('can_delete', $can_delete);
		}
	}

	public function actionOrder() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true) {
			if (($this->injector['URI']->action === 'up' || $this->injector['URI']->action === 'down') &&
					$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($this->injector['URI']->id)) == 1) {
				Core\Functions::moveOneStep($this->injector['URI']->action, 'gallery_pictures', 'id', 'pic', $this->injector['URI']->id, 'gallery_id');

				$gallery_id = $this->injector['Db']->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array($this->injector['URI']->id));

				GalleryFunctions::setGalleryCache($gallery_id);

				$this->injector['URI']->redirect('acp/gallery/edit/id_' . $gallery_id);
			}
		}
		$this->injector['URI']->redirect('errors/404');
	}

	public function actionSettings() {
		$settings = Core\Config::getSettings('gallery');
		$comments_active = Core\Modules::isActive('comments');

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
				$errors['dateformat'] = $this->injector['Lang']->t('system', 'select_date_format');
			if (Core\Validate::isNumber($_POST['sidebar']) === false)
				$errors['sidebar'] = $this->injector['Lang']->t('system', 'select_sidebar_entries');
			if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
				$errors[] = $this->injector['Lang']->t('gallery', 'select_use_overlay');
			if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
				$errors[] = $this->injector['Lang']->t('gallery', 'select_allow_comments');
			if (Core\Validate::isNumber($_POST['thumbwidth']) === false || Core\Validate::isNumber($_POST['width']) === false || Core\Validate::isNumber($_POST['maxwidth']) === false)
				$errors[] = $this->injector['Lang']->t('gallery', 'invalid_image_width_entered');
			if (Core\Validate::isNumber($_POST['thumbheight']) === false || Core\Validate::isNumber($_POST['height']) === false || Core\Validate::isNumber($_POST['maxheight']) === false)
				$errors[] = $this->injector['Lang']->t('gallery', 'invalid_image_height_entered');
			if (Core\Validate::isNumber($_POST['filesize']) === false)
				$errors['filesize'] = $this->injector['Lang']->t('gallery', 'invalid_image_filesize_entered');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
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

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/gallery');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			if ($comments_active === true) {
				$lang_comments = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
			}

			$lang_overlay = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

			$this->injector['View']->assign('dateformat', $this->injector['Date']->dateformatDropdown($settings['dateformat']));

			$this->injector['View']->assign('sidebar_entries', Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->injector['Session']->generateFormToken();
		}
	}

}