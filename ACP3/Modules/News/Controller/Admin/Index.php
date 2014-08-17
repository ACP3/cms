<?php

namespace ACP3\Modules\News\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Categories;
use ACP3\Modules\News;

/**
 * Class Index
 * @package ACP3\Modules\News\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var News\Model
     */
    protected $newsModel;

    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Helpers\Secure $secureHelper,
        News\Model $newsModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->newsModel = $newsModel;
    }

    public function actionCreate()
    {
        $config = new Core\Config($this->db, 'news');
        $settings = $config->getSettings();

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('news.validator');
                $validator->validateCreate($_POST);

                $insertValues = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
                    'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
                    'category_id' => strlen($_POST['cat_create']) >= 3 ? $this->get('categories.helpers')->categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
                    'uri' => Core\Functions::strEncode($_POST['uri'], true),
                    'target' => (int)$_POST['target'],
                    'link_title' => Core\Functions::strEncode($_POST['link_title']),
                    'user_id' => $this->auth->getUserId(),
                );

                $lastId = $this->newsModel->insert($insertValues);

                $this->aliases->insertUriAlias(
                    sprintf(News\Helpers::URL_KEY_PATTERN, $lastId),
                    $_POST['alias'],
                    $_POST['seo_keywords'],
                    $_POST['seo_description'],
                    (int)$_POST['seo_robots']
                );
                $this->seo->setCache();

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/news');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        // Datumsauswahl
        $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));

        // Kategorien
        $this->view->assign('categories', $this->get('categories.helpers')->categoriesList('news', '', true));

        // Weiterlesen & Kommentare
        if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && $this->modules->isActive('comments') === true)) {
            $i = 0;
            $options = array();
            if ($settings['readmore'] == 1) {
                $options[$i]['name'] = 'readmore';
                $options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', '0', 'checked');
                $options[$i]['lang'] = $this->lang->t('news', 'activate_readmore');
                $i++;
            }
            if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
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

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/news/index/delete', 'acp/news');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            $commentsInstalled = $this->modules->isInstalled('comments');
            $cache = new Core\Cache('news');

            foreach ($items as $item) {
                $bool = $this->newsModel->delete($item);
                if ($commentsInstalled === true) {
                    $this->get('comments.helpers')->deleteCommentsByModuleAndResult('news', $item);
                }

                $cache->delete(News\Cache::CACHE_ID . $item);
                $this->aliases->deleteUriAlias(sprintf(News\Helpers::URL_KEY_PATTERN, $item));
            }

            $this->seo->setCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/news');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $news = $this->newsModel->getOneById((int)$this->request->id);

        if (empty($news) === false) {
            $config = new Core\Config($this->db, 'news');
            $settings = $config->getSettings();

            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('news.validator');
                    $validator->validateEdit($_POST);

                    $updateValues = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
                        'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
                        'category_id' => strlen($_POST['cat_create']) >= 3 ? $this->get('categories.helpers')->categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
                        'uri' => Core\Functions::strEncode($_POST['uri'], true),
                        'target' => (int)$_POST['target'],
                        'link_title' => Core\Functions::strEncode($_POST['link_title']),
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->newsModel->update($updateValues, $this->request->id);

                    $this->aliases->insertUriAlias(
                        sprintf(News\Helpers::URL_KEY_PATTERN, $this->request->id),
                        $_POST['alias'],
                        $_POST['seo_keywords'],
                        $_POST['seo_description'],
                        (int)$_POST['seo_robots']
                    );
                    $this->seo->setCache();

                    $cache = new News\Cache($this->newsModel);
                    $cache->setCache($this->request->id);

                    $this->secureHelper->unsetFormToken($this->request->query);

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/news');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
                }
            }

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($news['start'], $news['end'])));

            // Kategorien
            $this->view->assign('categories', $this->get('categories.helpers')->categoriesList('news', $news['category_id'], true));

            // Weiterlesen & Kommentare
            if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && $this->modules->isActive('comments') === true)) {
                $i = 0;
                $options = array();
                if ($settings['readmore'] == 1) {
                    $options[$i]['name'] = 'readmore';
                    $options[$i]['checked'] = Core\Functions::selectEntry('readmore', '1', $news['readmore'], 'checked');
                    $options[$i]['lang'] = $this->lang->t('news', 'activate_readmore');
                    $i++;
                }
                if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                    $options[$i]['name'] = 'comments';
                    $options[$i]['checked'] = Core\Functions::selectEntry('comments', '1', $news['comments'], 'checked');
                    $options[$i]['lang'] = $this->lang->t('system', 'allow_comments');
                }
                $this->view->assign('options', $options);
            }

            // Linkziel
            $lang_target = array($this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank'));
            $this->view->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target, $news['target']));

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(News\Helpers::URL_KEY_PATTERN, $this->request->id)));

            $this->view->assign('form', array_merge($news, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $news = $this->newsModel->getAllInAcp();
        $c_news = count($news);

        if ($c_news > 0) {
            $canDelete = $this->modules->hasPermission('admin/news/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));

            for ($i = 0; $i < $c_news; ++$i) {
                $news[$i]['period'] = $this->date->formatTimeRange($news[$i]['start'], $news[$i]['end']);
            }
            $this->view->assign('news', $news);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        $config = new Core\Config($this->db, 'news');

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('news.validator');
                $validator->validateSettings($_POST, $this->lang);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'sidebar' => (int)$_POST['sidebar'],
                    'readmore' => $_POST['readmore'],
                    'readmore_chars' => (int)$_POST['readmore_chars'],
                    'category_in_breadcrumb' => $_POST['category_in_breadcrumb'],
                    'comments' => $_POST['comments'],
                );
                $bool = $config->setSettings($data);

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/news');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $settings = $config->getSettings();

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        $lang_readmore = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('readmore', Core\Functions::selectGenerator('readmore', array(1, 0), $lang_readmore, $settings['readmore'], 'checked'));

        $this->view->assign('readmore_chars', empty($_POST) === false ? $_POST['readmore_chars'] : $settings['readmore_chars']);

        if ($this->modules->isActive('comments') === true) {
            $lang_allow_comments = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('allow_comments', Core\Functions::selectGenerator('comments', array(1, 0), $lang_allow_comments, $settings['comments'], 'checked'));
        }

        $this->view->assign('sidebar_entries', Core\Functions::recordsPerPage((int)$settings['sidebar'], 1, 10));

        $lang_category_in_breadcrumb = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('category_in_breadcrumb', Core\Functions::selectGenerator('category_in_breadcrumb', array(1, 0), $lang_category_in_breadcrumb, $settings['category_in_breadcrumb'], 'checked'));

        $this->secureHelper->generateFormToken($this->request->query);
    }

}
