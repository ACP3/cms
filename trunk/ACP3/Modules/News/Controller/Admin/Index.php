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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var News\Model
     */
    protected $newsModel;
    /**
     * @var News\Cache
     */
    protected $newsCache;
    /**
     * @var Core\Config
     */
    protected $newsConfig;

    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        News\Model $newsModel,
        News\Cache $newsCache,
        Core\Config $newsConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->newsModel = $newsModel;
        $this->newsCache = $newsCache;
        $this->newsConfig = $newsConfig;
    }

    public function actionCreate()
    {
        $settings = $this->newsConfig->getSettings();

        if (empty($_POST) === false) {
            $this->_createPost($_POST, $settings);
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
            $settings = $this->newsConfig->getSettings();

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings);
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
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));

            $this->view->assign('news', $news);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
        }

        $settings = $this->newsConfig->getSettings();

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

    private function _createPost(array $formData, array $settings)
    {
        try {
            $validator = $this->get('news.validator');
            $validator->validateCreate($formData);

            $insertValues = array(
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'readmore' => $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0,
                'comments' => $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0,
                'category_id' => strlen($formData['cat_create']) >= 3 ? $this->get('categories.helpers')->categoriesCreate($formData['cat_create'], 'news') : $formData['cat'],
                'uri' => Core\Functions::strEncode($formData['uri'], true),
                'target' => (int)$formData['target'],
                'link_title' => Core\Functions::strEncode($formData['link_title']),
                'user_id' => $this->auth->getUserId(),
            );

            $lastId = $this->newsModel->insert($insertValues);

            $this->aliases->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
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

    private function _editPost(array $formData, array $settings)
    {
        try {
            $validator = $this->get('news.validator');
            $validator->validateEdit($formData);

            $updateValues = array(
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'readmore' => $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0,
                'comments' => $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0,
                'category_id' => strlen($formData['cat_create']) >= 3 ? $this->get('categories.helpers')->categoriesCreate($formData['cat_create'], 'news') : $formData['cat'],
                'uri' => Core\Functions::strEncode($formData['uri'], true),
                'target' => (int)$formData['target'],
                'link_title' => Core\Functions::strEncode($formData['link_title']),
                'user_id' => $this->auth->getUserId(),
            );

            $bool = $this->newsModel->update($updateValues, $this->request->id);

            $this->aliases->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $this->request->id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
            $this->seo->setCache();

            $this->newsCache->setCache($this->request->id);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/news');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    private function _settingsPost(array $formData)
    {
        try {
            $validator = $this->get('news.validator');
            $validator->validateSettings($formData, $this->lang);

            $data = array(
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar'],
                'readmore' => $formData['readmore'],
                'readmore_chars' => (int)$formData['readmore_chars'],
                'category_in_breadcrumb' => $formData['category_in_breadcrumb'],
                'comments' => $formData['comments'],
            );
            $bool = $this->newsConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/news');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/news');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}
