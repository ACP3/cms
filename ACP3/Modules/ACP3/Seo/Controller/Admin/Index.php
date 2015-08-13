<?php

namespace ACP3\Modules\ACP3\Seo\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model
     */
    protected $seoModel;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validator
     */
    protected $seoValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Cache               $seoCache
     * @param \ACP3\Modules\ACP3\Seo\Model               $seoModel
     * @param \ACP3\Modules\ACP3\Seo\Validator           $seoValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Cache $seoCache,
        Seo\Model $seoModel,
        Seo\Validator $seoValidator)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->seoCache = $seoCache;
        $this->seoModel = $seoModel;
        $this->seoValidator = $seoValidator;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge(['uri' => ''], $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function($items) {
                $bool = false;

                foreach ($items as $item) {
                    $bool = $this->seoModel->delete($item);
                }

                $this->seoCache->saveCache();

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $seo = $this->seoModel->getOneById($id);

        if (empty($seo) === false) {
            $this->breadcrumb->setTitlePostfix($seo['alias']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $seo['uri'], $id);
            }

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields($seo['uri']));

            $this->view->assign('form', array_merge(['uri' => $seo['uri']], $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $seo = $this->seoModel->getAllInAcp();

        if (count($seo) > 0) {
            $canDelete = $this->acl->hasPermission('admin/seo/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('seo', $seo);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        $this->actionHelper->handleCreatePostAction(function() use ($formData) {
            $this->seoValidator->validate($formData);

            $bool = $this->seo->insertUriAlias(
                $formData['uri'],
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @param array  $formData
     * @param string $path
     * @param int    $id
     */
    protected function _editPost(array $formData, $path, $id)
    {
        $this->actionHelper->handleEditPostAction(function() use ($formData, $path, $id) {
            $this->seoValidator->validate($formData, $path);

            $updateValues = [
                'uri' => $formData['uri'],
                'alias' => $formData['alias'],
                'keywords' => Core\Functions::strEncode($formData['seo_keywords']),
                'description' => Core\Functions::strEncode($formData['seo_description']),
                'robots' => (int)$formData['seo_robots']
            ];

            $bool = $this->seoModel->update($updateValues, $id);

            $this->seoCache->saveCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll());
        }

        $seoSettings = $this->config->getSettings('seo');

        // Robots
        $lang_robots = [
            $this->lang->t('seo', 'robots_index_follow'),
            $this->lang->t('seo', 'robots_index_nofollow'),
            $this->lang->t('seo', 'robots_noindex_follow'),
            $this->lang->t('seo', 'robots_noindex_nofollow')
        ];
        $this->view->assign('robots', $this->get('core.helpers.forms')->selectGenerator('robots', [1, 2, 3, 4], $lang_robots, $seoSettings['robots']));

        // Sef-URIs
        $lang_modRewrite = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('mod_rewrite', $this->get('core.helpers.forms')->checkboxGenerator('mod_rewrite', [1, 0], $lang_modRewrite, $seoSettings['mod_rewrite']));

        $this->view->assign('form', array_merge($seoSettings, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        $this->actionHelper->handleSettingsPostAction(function() use ($formData) {
            $this->seoValidator->validateSettings($formData);

            // Config aktualisieren
            $data = [
                'meta_description' => Core\Functions::strEncode($formData['meta_description']),
                'meta_keywords' => Core\Functions::strEncode($formData['meta_keywords']),
                'mod_rewrite' => (int)$formData['mod_rewrite'],
                'robots' => (int)$formData['robots'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $bool = $this->config->setSettings($data, 'seo');

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}