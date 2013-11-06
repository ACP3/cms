<?php

namespace ACP3\Modules\Gallery;

use ACP3\Core;

/**
 * Description of GalleryAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\AdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function actionCreate()
    {
        if (isset($_POST['submit']) === true) {
            if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
                $errors[] = $this->lang->t('system', 'select_date');
            if (strlen($_POST['title']) < 3)
                $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
            if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
                (Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias']) === true)
            )
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
                    'user_id' => $this->auth->getUserId(),
                );

                $bool = $this->db->insert(DB_PRE . 'gallery', $insert_values);
                if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
                    Core\SEO::insertUriAlias('gallery/pics/id_' . $this->db->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/gallery');
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));

            $this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

            $this->session->generateFormToken();
        }
    }

    public function actionCreatePicture()
    {
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->uri->id)) == 1
        ) {
            $gallery = $this->db->fetchColumn('SELECT title FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->uri->id));

            $this->breadcrumb
                ->append($gallery, $this->uri->route('acp/gallery/edit/id_' . $this->uri->id))
                ->append($this->lang->t('gallery', 'acp_create_picture'));

            $settings = Core\Config::getSettings('gallery');

            if (isset($_POST['submit']) === true) {
                $file['tmp_name'] = $_FILES['file']['tmp_name'];
                $file['name'] = $_FILES['file']['name'];
                $file['size'] = $_FILES['file']['size'];

                if (empty($file['tmp_name']))
                    $errors['file'] = $this->lang->t('gallery', 'no_picture_selected');
                if (!empty($file['tmp_name']) &&
                    (Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
                        $_FILES['file']['error'] !== UPLOAD_ERR_OK)
                )
                    $errors['file'] = $this->lang->t('gallery', 'invalid_image_selected');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
                    $picNum = $this->db->fetchColumn('SELECT MAX(pic) FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($this->uri->id));

                    $insert_values = array(
                        'id' => '',
                        'pic' => !is_null($picNum) ? $picNum + 1 : 1,
                        'gallery_id' => $this->uri->id,
                        'file' => $result['name'],
                        'description' => Core\Functions::strEncode($_POST['description'], true),
                        'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
                    );

                    $bool = $this->db->insert(DB_PRE . 'gallery_pictures', $insert_values);
                    $bool2 = Helpers::generatePictureAlias($this->db->lastInsertId());
                    Helpers::setGalleryCache($this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool && $bool2, $this->lang->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/edit/id_' . $this->uri->id);
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
                    $options = array();
                    $options[0]['name'] = 'comments';
                    $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', '0', 'checked');
                    $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                    $this->view->assign('options', $options);
                }

                $galleries = $this->db->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'gallery ORDER BY start DESC');
                $c_galleries = count($galleries);
                for ($i = 0; $i < $c_galleries; ++$i) {
                    $galleries[$i]['selected'] = Core\Functions::selectEntry('gallery', $galleries[$i]['id'], $this->uri->id);
                    $galleries[$i]['date'] = $this->date->format($galleries[$i]['start']);
                }

                $this->view->assign('galleries', $galleries);
                $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('description' => ''));
                $this->view->assign('gallery_id', $this->uri->id);

                $this->session->generateFormToken();
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/gallery/delete', 'acp/gallery');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = $bool2 = false;

            foreach ($items as $item) {
                if (!empty($item) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($item)) == 1) {
                    // Hochgeladene Bilder löschen
                    $pictures = $this->db->fetchAll('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($item));
                    foreach ($pictures as $row) {
                        removePicture($row['file']);
                    }
                    // Galerie Cache löschen
                    Core\Cache::delete('pics_id_' . $item, 'gallery');
                    Core\SEO::deleteUriAlias('gallery/pics/id_' . $item);
                    Helpers::deletePictureAliases($item);

                    // Fotogalerie mitsamt Bildern löschen
                    $bool = $this->db->delete(DB_PRE . 'gallery', array('id' => $item));
                    $bool2 = $this->db->delete(DB_PRE . 'gallery_pictures', array('gallery_id' => $item));
                }
            }
            Core\Functions::setRedirectMessage($bool && $bool2, $this->lang->t('system', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/gallery');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionDeletePicture()
    {
        $items = $this->_deleteItem('acp/gallery/delete_picture', 'acp/gallery/edit/id_' . $this->uri->id);

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                if (!empty($item) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($item)) == 1) {
                    // Datei ebenfalls löschen
                    $picture = $this->db->fetchAssoc('SELECT pic, gallery_id, file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($item));
                    $this->db->executeUpdate('UPDATE ' . DB_PRE . 'gallery_pictures SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', array($picture['pic'], $picture['gallery_id']));
                    Helpers::removePicture($picture['file']);

                    $bool = $this->db->delete(DB_PRE . 'gallery_pictures', array('id' => $item));
                    Core\SEO::deleteUriAlias('gallery/details/id_' . $item);
                    Helpers::setGalleryCache($picture['gallery_id']);
                }
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->uri->id)) == 1
        ) {
            $gallery = $this->db->fetchAssoc('SELECT start, end, title FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->uri->id));

            $this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields('gallery/pics/id_' . $this->uri->id));

            $this->breadcrumb->append($gallery['title']);

            if (isset($_POST['submit']) === true) {
                if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
                    $errors[] = $this->lang->t('system', 'select_date');
                if (strlen($_POST['title']) < 3)
                    $errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
                if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
                    (Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'gallery/pics/id_' . $this->uri->id))
                )
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
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->db->update(DB_PRE . 'gallery', $update_values, array('id' => $this->uri->id));
                    if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
                        Core\SEO::insertUriAlias('gallery/pics/id_' . $this->uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                        Helpers::generatePictureAliases($this->uri->id);
                    }

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
                }
            }
            if (isset($_POST['entries']) === false && isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                Core\Functions::getRedirectMessage();

                $this->view->assign('gallery_id', $this->uri->id);

                // Datumsauswahl
                $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($gallery['start'], $gallery['end'])));

                $this->view->assign('form', isset($_POST['submit']) ? $_POST : $gallery);

                $pictures = $this->db->fetchAll('SELECT id, pic, file, description FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ? ORDER BY pic ASC', array($this->uri->id));
                $c_pictures = count($pictures);

                if ($c_pictures > 0) {
                    $can_delete = Core\Modules::hasPermission('gallery', 'acp_delete_picture');
                    $config = array(
                        'element' => '#acp-table',
                        'hide_col_sort' => $can_delete === true ? 0 : ''
                    );
                    $this->view->appendContent(Core\Functions::datatable($config));

                    for ($i = 0; $i < $c_pictures; ++$i) {
                        $pictures[$i]['first'] = $i == 0 ? true : false;
                        $pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
                    }
                    $this->view->assign('pictures', $pictures);
                    $this->view->assign('can_delete', $can_delete);
                    $this->view->assign('can_order', Core\Modules::hasPermission('gallery', 'acp_order'));
                    $this->view->assign('can_edit_picture', Core\Modules::hasPermission('gallery', 'acp_edit_picture'));
                }

                $this->session->generateFormToken();
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEditPicture()
    {
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($this->uri->id)) == 1
        ) {
            $picture = $this->db->fetchAssoc('SELECT p.gallery_id, p.file, p.description, p.comments, g.title AS gallery_title FROM ' . DB_PRE . 'gallery_pictures AS p, ' . DB_PRE . 'gallery AS g WHERE p.id = ? AND p.gallery_id = g.id', array($this->uri->id));

            $this->breadcrumb
                ->append($picture['gallery_title'], $this->uri->route('acp/gallery/edit/id_' . $picture['gallery_id']))
                ->append($this->lang->t('gallery', 'acp_edit_picture'));

            $settings = Core\Config::getSettings('gallery');

            if (isset($_POST['submit']) === true) {
                if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
                    $file['tmp_name'] = $_FILES['file']['tmp_name'];
                    $file['name'] = $_FILES['file']['name'];
                    $file['size'] = $_FILES['file']['size'];
                }

                if (!empty($file['tmp_name']) &&
                    (Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
                        $_FILES['file']['error'] !== UPLOAD_ERR_OK)
                )
                    $errors['file'] = $this->lang->t('gallery', 'invalid_image_selected');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    $new_file_sql = null;
                    if (isset($file) && is_array($file)) {
                        $result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
                        $new_file_sql['file'] = $result['name'];
                    }

                    $update_values = array(
                        'description' => Core\Functions::strEncode($_POST['description'], true),
                        'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
                    );
                    if (is_array($new_file_sql) === true) {
                        $old_file = $this->db->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($this->uri->id));
                        Helpers::removePicture($old_file);

                        $update_values = array_merge($update_values, $new_file_sql);
                    }

                    $bool = $this->db->update(DB_PRE . 'gallery_pictures', $update_values, array('id' => $this->uri->id));
                    Helpers::setGalleryCache($picture['gallery_id']);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                if ($settings['overlay'] == 0 && $settings['comments'] == 1 && Core\Modules::isActive('comments') === true) {
                    $options = array();
                    $options[0]['name'] = 'comments';
                    $options[0]['checked'] = Core\Functions::selectEntry('comments', '1', $picture['comments'], 'checked');
                    $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                    $this->view->assign('options', $options);
                }

                $this->view->assign('form', isset($_POST['submit']) ? $_POST : $picture);
                $this->view->assign('gallery_id', $this->uri->id);

                $this->session->generateFormToken();
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $galleries = $this->db->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . DB_PRE . 'gallery AS g LEFT JOIN ' . DB_PRE . 'gallery_pictures AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
        $c_galleries = count($galleries);

        if ($c_galleries > 0) {
            $can_delete = Core\Modules::hasPermission('gallery', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::datatable($config));
            for ($i = 0; $i < $c_galleries; ++$i) {
                $galleries[$i]['period'] = $this->date->formatTimeRange($galleries[$i]['start'], $galleries[$i]['end']);
            }
            $this->view->assign('galleries', $galleries);
            $this->view->assign('can_delete', $can_delete);
        }
    }

    public function actionOrder()
    {
        if (Core\Validate::isNumber($this->uri->id) === true) {
            if (($this->uri->action === 'up' || $this->uri->action === 'down') &&
                $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($this->uri->id)) == 1
            ) {
                Core\Functions::moveOneStep($this->uri->action, 'gallery_pictures', 'id', 'pic', $this->uri->id, 'gallery_id');

                $gallery_id = $this->db->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array($this->uri->id));

                Helpers::setGalleryCache($gallery_id);

                $this->uri->redirect('acp/gallery/edit/id_' . $gallery_id);
            }
        }
        $this->uri->redirect('errors/404');
    }

    public function actionSettings()
    {
        $settings = Core\Config::getSettings('gallery');
        $comments_active = Core\Modules::isActive('comments');

        if (isset($_POST['submit']) === true) {
            if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
                $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
            if (Core\Validate::isNumber($_POST['sidebar']) === false)
                $errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
            if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
                $errors[] = $this->lang->t('gallery', 'select_use_overlay');
            if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
                $errors[] = $this->lang->t('gallery', 'select_allow_comments');
            if (Core\Validate::isNumber($_POST['thumbwidth']) === false || Core\Validate::isNumber($_POST['width']) === false || Core\Validate::isNumber($_POST['maxwidth']) === false)
                $errors[] = $this->lang->t('gallery', 'invalid_image_width_entered');
            if (Core\Validate::isNumber($_POST['thumbheight']) === false || Core\Validate::isNumber($_POST['height']) === false || Core\Validate::isNumber($_POST['maxheight']) === false)
                $errors[] = $this->lang->t('gallery', 'invalid_image_height_entered');
            if (Core\Validate::isNumber($_POST['filesize']) === false)
                $errors['filesize'] = $this->lang->t('gallery', 'invalid_image_filesize_entered');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
                $data = array(
                    'width' => (int)$_POST['width'],
                    'height' => (int)$_POST['height'],
                    'thumbwidth' => (int)$_POST['thumbwidth'],
                    'thumbheight' => (int)$_POST['thumbheight'],
                    'maxwidth' => (int)$_POST['maxwidth'],
                    'maxheight' => (int)$_POST['maxheight'],
                    'filesize' => (int)$_POST['filesize'],
                    'overlay' => $_POST['overlay'],
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'sidebar' => (int)$_POST['sidebar'],
                );
                if ($comments_active === true)
                    $data['comments'] = $_POST['comments'];

                $bool = Core\Config::setSettings('gallery', $data);

                // Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
                if ($_POST['thumbwidth'] !== $settings['thumbwidth'] || $_POST['thumbheight'] !== $settings['thumbheight'] ||
                    $_POST['width'] !== $settings['width'] || $_POST['height'] !== $settings['height']
                ) {
                    Core\Cache::purge('images', 'gallery');
                    Core\Cache::purge('sql', 'gallery');
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/gallery');
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            if ($comments_active === true) {
                $lang_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
                $this->view->assign('comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
            }

            $lang_overlay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

            $this->view->assign('dateformat', $this->date->dateformatDropdown($settings['dateformat']));

            $this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int)$settings['sidebar'], 1, 10));

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

            $this->session->generateFormToken();
        }
    }

}