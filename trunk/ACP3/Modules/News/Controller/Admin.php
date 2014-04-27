<?php

namespace ACP3\Modules\News\Controller;

use ACP3\Core;
use ACP3\Modules\Categories;
use ACP3\Modules\News;

/**
 * Description of NewsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var News\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new News\Model($this->db, $this->lang, $this->uri);
    }

    public function actionCreate()
    {
        $settings = Core\Config::getSettings('news');

        if (empty($_POST) === false) {
            try {
                $this->model->validateCreate($_POST);

                $insertValues = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
                    'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
                    'category_id' => strlen($_POST['cat_create']) >= 3 ? Categories\Helpers::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
                    'uri' => Core\Functions::strEncode($_POST['uri'], true),
                    'target' => (int)$_POST['target'],
                    'link_title' => Core\Functions::strEncode($_POST['link_title']),
                    'user_id' => $this->auth->getUserId(),
                );

                $lastId = $this->model->insert($insertValues);
                if ((bool)CONFIG_SEO_ALIASES === true) {
                    $this->uri->insertUriAlias('news/details/id_' . $lastId, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                    $this->seo->setCache();
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/news');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

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

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $defaults = array('title' => '', 'text' => '', 'uri' => '', 'link_title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => '');
        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/news/delete', 'acp/news');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            $commentsInstalled = Core\Modules::isInstalled('comments');
            foreach ($items as $item) {
                $bool = $this->model->delete($item);
                if ($commentsInstalled === true) {
                    \ACP3\Modules\Comments\Helpers::deleteCommentsByModuleAndResult('news', $item);
                }
                // News Cache löschen
                Core\Cache::delete('details_id_' . $item, 'news');
                $this->uri->deleteUriAlias('news/details/id_' . $item);
            }

            $this->seo->setCache();

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/news');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $news = $this->model->getOneById((int)$this->uri->id);

        if (empty($news) === false) {
            $settings = Core\Config::getSettings('news');

            if (empty($_POST) === false) {
                try {
                    $this->model->validateEdit($_POST);

                    $updateValues = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
                        'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
                        'category_id' => strlen($_POST['cat_create']) >= 3 ? Categories\Helpers::categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
                        'uri' => Core\Functions::strEncode($_POST['uri'], true),
                        'target' => (int)$_POST['target'],
                        'link_title' => Core\Functions::strEncode($_POST['link_title']),
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->model->update($updateValues, $this->uri->id);

                    if ((bool)CONFIG_SEO_ALIASES === true) {
                        $this->uri->insertUriAlias('news/details/id_' . $this->uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                        $this->seo->setCache();
                    }

                    $this->model->setCache($this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/news');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

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

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields('news/details/id_' . $this->uri->id));

            $this->view->assign('form', array_merge($news, $_POST));

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $news = $this->model->getAllInAcp();
        $c_news = count($news);

        if ($c_news > 0) {
            $can_delete = Core\Modules::hasPermission('news', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));

            for ($i = 0; $i < $c_news; ++$i) {
                $news[$i]['period'] = $this->date->formatTimeRange($news[$i]['start'], $news[$i]['end']);
            }
            $this->view->assign('news', $news);
            $this->view->assign('can_delete', $can_delete);
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validateSettings($_POST, $this->lang);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'sidebar' => (int)$_POST['sidebar'],
                    'readmore' => $_POST['readmore'],
                    'readmore_chars' => (int)$_POST['readmore_chars'],
                    'category_in_breadcrumb' => $_POST['category_in_breadcrumb'],
                    'comments' => $_POST['comments'],
                );
                $bool = Core\Config::setSettings('news', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/news');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = Core\Config::getSettings('news');

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        $lang_readmore = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('readmore', Core\Functions::selectGenerator('readmore', array(1, 0), $lang_readmore, $settings['readmore'], 'checked'));

        $this->view->assign('readmore_chars', empty($_POST) === false ? $_POST['readmore_chars'] : $settings['readmore_chars']);

        if (Core\Modules::isActive('comments') === true) {
            $lang_allow_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('allow_comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_allow_comments, $settings['comments'], 'checked'));
        }

        $this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int)$settings['sidebar'], 1, 10));

        $lang_category_in_breadcrumb = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('category_in_breadcrumb', Core\Functions::selectGenerator('category_in_breadcrumb', array(1, 0), $lang_category_in_breadcrumb, $settings['category_in_breadcrumb'], 'checked'));

        $this->session->generateFormToken();
    }

}
