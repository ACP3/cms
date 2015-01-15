<?php

namespace ACP3\Modules\News\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Categories;
use ACP3\Modules\Comments;
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
    /**
     * @var \ACP3\Modules\News\Validator
     */
    protected $newsValidator;
    /**
     * @var \ACP3\Modules\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Modules\Comments\Helpers
     */
    protected $commentsHelpers;

    /**
     * @param \ACP3\Core\Context\Admin         $context
     * @param \ACP3\Core\Date                  $date
     * @param \ACP3\Core\Helpers\Secure        $secureHelper
     * @param \ACP3\Modules\News\Model         $newsModel
     * @param \ACP3\Modules\News\Cache         $newsCache
     * @param \ACP3\Core\Config                $newsConfig
     * @param \ACP3\Modules\News\Validator     $newsValidator
     * @param \ACP3\Modules\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        News\Model $newsModel,
        News\Cache $newsCache,
        Core\Config $newsConfig,
        News\Validator $newsValidator,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->newsModel = $newsModel;
        $this->newsCache = $newsCache;
        $this->newsConfig = $newsConfig;
        $this->newsValidator = $newsValidator;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param \ACP3\Modules\Comments\Helpers $commentsHelpers
     *
     * @return $this
     */
    public function setCommentsHelpers(Comments\Helpers $commentsHelpers)
    {
        $this->commentsHelpers = $commentsHelpers;

        return $this;
    }

    public function actionCreate()
    {
        $settings = $this->newsConfig->getSettings();

        if (empty($_POST) === false) {
            $this->_createPost($_POST, $settings);
        }

        // Datumsauswahl
        $this->view->assign('publication_period', $this->date->datepicker(['start', 'end']));

        // Kategorien
        $this->view->assign('categories', $this->categoriesHelpers->categoriesList('news', '', true));

        // Weiterlesen & Kommentare
        if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && $this->modules->isActive('comments') === true)) {
            $options = [];
            if ($settings['readmore'] == 1) {
                $options[] = [
                    'name' => 'readmore',
                    'checked' => $this->get('core.helpers.forms')->selectEntry('readmore', '1', '0', 'checked'),
                    'lang' => $this->lang->t('news', 'activate_readmore')
                ];
            }
            if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options[] = [
                    'name' => 'comments',
                    'checked' => $this->get('core.helpers.forms')->selectEntry('comments', '1', '0', 'checked'),
                    'lang' => $this->lang->t('system', 'allow_comments')
                ];
            }
            $this->view->assign('options', $options);
        }

        // Linkziel
        $lang_target = [$this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank')];
        $this->view->assign('target', $this->get('core.helpers.forms')->selectGenerator('target', [1, 2], $lang_target));

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $defaults = [
            'title' => '',
            'text' => '',
            'uri' => '',
            'link_title' => ''
        ];
        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->newsModel->delete($item);
                if ($this->commentsHelpers) {
                    $this->commentsHelpers->deleteCommentsByModuleAndResult('news', $item);
                }

                $this->newsCache->getCacheDriver()->delete(News\Cache::CACHE_ID . $item);
                $this->seo->deleteUriAlias(sprintf(News\Helpers::URL_KEY_PATTERN, $item));
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $news = $this->newsModel->getOneById((int)$this->request->id);

        if (empty($news) === false) {
            $this->breadcrumb->setTitlePostfix($news['title']);

            $settings = $this->newsConfig->getSettings();

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings);
            }

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(['start', 'end'], [$news['start'], $news['end']]));

            // Kategorien
            $this->view->assign('categories', $this->categoriesHelpers->categoriesList('news', $news['category_id'], true));

            // Weiterlesen & Kommentare
            if ($settings['readmore'] == 1 || ($settings['comments'] == 1 && $this->modules->isActive('comments') === true)) {
                $options = [];
                if ($settings['readmore'] == 1) {
                    $options[] = [
                        'name' => 'readmore',
                        'checked' => $this->get('core.helpers.forms')->selectEntry('readmore', '1', $news['readmore'], 'checked'),
                        'lang' => $this->lang->t('news', 'activate_readmore')
                    ];
                }
                if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                    $options[] = [
                        'name' => 'comments',
                        'checked' => $this->get('core.helpers.forms')->selectEntry('comments', '1', $news['comments'], 'checked'),
                        'lang' => $this->lang->t('system', 'allow_comments')
                    ];
                }
                $this->view->assign('options', $options);
            }

            // Linkziel
            $lang_target = [$this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank')];
            $this->view->assign('target', $this->get('core.helpers.forms')->selectGenerator('target', [1, 2], $lang_target, $news['target']));

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(News\Helpers::URL_KEY_PATTERN, $this->request->id)));

            $this->view->assign('form', array_merge($news, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $news = $this->newsModel->getAllInAcp();

        if (count($news) > 0) {
            $canDelete = $this->acl->hasPermission('admin/news/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
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

        $lang_readmore = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('readmore', $this->get('core.helpers.forms')->selectGenerator('readmore', [1, 0], $lang_readmore, $settings['readmore'], 'checked'));

        $this->view->assign('readmore_chars', empty($_POST) === false ? $_POST['readmore_chars'] : $settings['readmore_chars']);

        if ($this->modules->isActive('comments') === true) {
            $lang_allow_comments = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('allow_comments', $this->get('core.helpers.forms')->selectGenerator('comments', [1, 0], $lang_allow_comments, $settings['comments'], 'checked'));
        }

        $this->view->assign('sidebar_entries', $this->get('core.helpers.forms')->recordsPerPage((int)$settings['sidebar'], 1, 10));

        $lang_category_in_breadcrumb = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('category_in_breadcrumb', $this->get('core.helpers.forms')->selectGenerator('category_in_breadcrumb', [1, 0], $lang_category_in_breadcrumb, $settings['category_in_breadcrumb'], 'checked'));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    private function _createPost(array $formData, array $settings)
    {
        try {
            $this->newsValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'readmore' => $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0,
                'comments' => $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0,
                'category_id' => !empty($formData['cat_create']) ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], 'news') : $formData['cat'],
                'uri' => Core\Functions::strEncode($formData['uri'], true),
                'target' => (int)$formData['target'],
                'link_title' => Core\Functions::strEncode($formData['link_title']),
                'user_id' => $this->auth->getUserId(),
            ];

            $lastId = $this->newsModel->insert($insertValues);

            $this->seo->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    private function _editPost(array $formData, array $settings)
    {
        try {
            $this->newsValidator->validate(
                $formData,
                sprintf(News\Helpers::URL_KEY_PATTERN, $this->request->id)
            );

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'readmore' => $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0,
                'comments' => $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0,
                'category_id' => strlen($formData['cat_create']) >= 3 ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], 'news') : $formData['cat'],
                'uri' => Core\Functions::strEncode($formData['uri'], true),
                'target' => (int)$formData['target'],
                'link_title' => Core\Functions::strEncode($formData['link_title']),
                'user_id' => $this->auth->getUserId(),
            ];

            $bool = $this->newsModel->update($updateValues, $this->request->id);

            $this->seo->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $this->request->id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->newsCache->setCache($this->request->id);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _settingsPost(array $formData)
    {
        try {
            $this->newsValidator->validateSettings($formData, $this->lang);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar'],
                'readmore' => $formData['readmore'],
                'readmore_chars' => (int)$formData['readmore_chars'],
                'category_in_breadcrumb' => $formData['category_in_breadcrumb'],
                'comments' => $formData['comments'],
            ];
            $bool = $this->newsConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
