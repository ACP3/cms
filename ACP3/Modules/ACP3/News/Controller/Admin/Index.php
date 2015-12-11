<?php

namespace ACP3\Modules\ACP3\News\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\News;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\News\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\News\Cache
     */
    protected $newsCache;
    /**
     * @var \ACP3\Modules\ACP3\News\Validation\FormValidation
     */
    protected $newsValidator;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    protected $commentsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext        $context
     * @param \ACP3\Core\Date                                   $date
     * @param \ACP3\Core\Helpers\FormToken                      $formTokenHelper
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository      $newsRepository
     * @param \ACP3\Modules\ACP3\News\Cache                     $newsCache
     * @param \ACP3\Modules\ACP3\News\Validation\FormValidation $newsValidator
     * @param \ACP3\Modules\ACP3\Categories\Helpers             $categoriesHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        News\Model\NewsRepository $newsRepository,
        News\Cache $newsCache,
        News\Validation\FormValidation $newsValidator,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsRepository = $newsRepository;
        $this->newsCache = $newsCache;
        $this->newsValidator = $newsValidator;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param \ACP3\Modules\ACP3\Comments\Helpers $commentsHelpers
     *
     * @return $this
     */
    public function setCommentsHelpers(Comments\Helpers $commentsHelpers)
    {
        $this->commentsHelpers = $commentsHelpers;

        return $this;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        $settings = $this->config->getSettings('news');

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all(), $settings);
        }

        $defaults = [
            'title' => '',
            'text' => '',
            'uri' => '',
            'link_title' => '',
            'start' => '',
            'end' => ''
        ];

        $this->formTokenHelper->generateFormToken();

        return [
            'categories' => $this->categoriesHelpers->categoriesList('news', '', true),
            'options' => $this->fetchNewsOptions($settings, 0, 0),
            'target' => $this->get('core.helpers.forms')->linkTargetSelectGenerator('target'),
            'SEO_FORM_FIELDS' => $this->seo->formFields(),
            'form' => array_merge($defaults, $this->request->getPost()->all())
        ];
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    $bool = $this->newsRepository->delete($item);
                    if ($this->commentsHelpers) {
                        $this->commentsHelpers->deleteCommentsByModuleAndResult('news', $item);
                    }

                    $this->newsCache->getCacheDriver()->delete(News\Cache::CACHE_ID . $item);
                    $this->seo->deleteUriAlias(sprintf(News\Helpers::URL_KEY_PATTERN, $item));
                }

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $news = $this->newsRepository->getOneById($id);

        if (empty($news) === false) {
            $this->breadcrumb->setTitlePostfix($news['title']);

            $settings = $this->config->getSettings('news');

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $settings, $id);
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'categories' => $this->categoriesHelpers->categoriesList('news', $news['category_id'], true),
                'options' => $this->fetchNewsOptions($settings, $news['readmore'], $news['comments']),
                'target' => $this->get('core.helpers.forms')->linkTargetSelectGenerator('target', $news['target']),
                'SEO_FORM_FIELDS' => $this->seo->formFields(sprintf(News\Helpers::URL_KEY_PATTERN, $id)),
                'form' => array_merge($news, $this->request->getPost()->all())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $news = $this->newsRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($news)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/news/index/delete')
            ->setResourcePathEdit('admin/news/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'publication_period'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::NAME,
                'fields' => ['start', 'end'],
                'default_sort' => true
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('news', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['title'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('categories', 'category'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['cat']
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::NAME,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($news) > 0
        ];
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('news');

        if ($this->modules->isActive('comments') === true) {
            $this->view->assign('allow_comments', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('comments', $settings['comments']));
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'readmore' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('readmore', $settings['readmore']),
            'readmore_chars' => $this->request->getPost()->get('readmore_chars', $settings['readmore_chars']),
            'sidebar_entries' => $this->get('core.helpers.forms')->recordsPerPage((int)$settings['sidebar'], 1, 10),
            'category_in_breadcrumb' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('category_in_breadcrumb', $settings['category_in_breadcrumb'])
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData, array $settings)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData, $settings) {
            $this->newsValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'readmore' => $this->useReadMore($formData, $settings),
                'comments' => $this->useComments($formData, $settings),
                'category_id' => $this->fetchCategoryIdForSave($formData),
                'uri' => Core\Functions::strEncode($formData['uri'], true),
                'target' => (int)$formData['target'],
                'link_title' => Core\Functions::strEncode($formData['link_title']),
                'user_id' => $this->user->getUserId(),
            ];

            $lastId = $this->newsRepository->insert($insertValues);

            $this->seo->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, array $settings, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $id) {
            $this->newsValidator->validate(
                $formData,
                sprintf(News\Helpers::URL_KEY_PATTERN, $id)
            );

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'readmore' => $this->useReadMore($formData, $settings),
                'comments' => $this->useComments($formData, $settings),
                'category_id' => $this->fetchCategoryIdForSave($formData),
                'uri' => Core\Functions::strEncode($formData['uri'], true),
                'target' => (int)$formData['target'],
                'link_title' => Core\Functions::strEncode($formData['link_title']),
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->newsRepository->update($updateValues, $id);

            $this->seo->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->newsCache->saveCache($id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->newsValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar'],
                'readmore' => $formData['readmore'],
                'readmore_chars' => (int)$formData['readmore_chars'],
                'category_in_breadcrumb' => $formData['category_in_breadcrumb'],
            ];

            if ($this->commentsHelpers) {
                $data['comments'] = $formData['comments'];
            }

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'news');
        });
    }

    /**
     * @param array $formData
     *
     * @return int
     */
    protected function fetchCategoryIdForSave(array $formData)
    {
        return !empty($formData['cat_create']) ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], 'news') : $formData['cat'];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useReadMore(array $formData, array $settings)
    {
        return $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0;
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useComments(array $formData, array $settings)
    {
        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }

    /**
     * @param array $settings
     * @param int   $readmoreValue
     * @param int   $commentsValue
     *
     * @return array
     */
    protected function fetchNewsOptions(array $settings, $readmoreValue, $commentsValue)
    {
        $options = [];
        if ($settings['readmore'] == 1) {
            $options[] = [
                'name' => 'readmore',
                'checked' => $this->get('core.helpers.forms')->selectEntry('readmore', '1', $readmoreValue, 'checked'),
                'lang' => $this->translator->t('news', 'activate_readmore')
            ];
        }
        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $options[] = [
                'name' => 'comments',
                'checked' => $this->get('core.helpers.forms')->selectEntry('comments', '1', $commentsValue, 'checked'),
                'lang' => $this->translator->t('system', 'allow_comments')
            ];
        }

        return $options;
    }
}
