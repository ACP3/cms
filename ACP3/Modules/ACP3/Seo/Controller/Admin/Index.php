<?php

namespace ACP3\Modules\ACP3\Seo\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
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
     * @param \ACP3\Core\Context\Admin    $context
     * @param \ACP3\Core\Helpers\Secure   $secureHelper
     * @param \ACP3\Modules\ACP3\Seo\Cache     $seoCache
     * @param \ACP3\Modules\ACP3\Seo\Model     $seoModel
     * @param \ACP3\Modules\ACP3\Seo\Validator $seoValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Seo\Cache $seoCache,
        Seo\Model $seoModel,
        Seo\Validator $seoValidator)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->seoCache = $seoCache;
        $this->seoModel = $seoModel;
        $this->seoValidator = $seoValidator;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge(['uri' => ''], $_POST));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->seoModel->delete($item);
            }

            $this->seoCache->setCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $seo = $this->seoModel->getOneById((int)$this->request->id);

        if (empty($seo) === false) {
            $this->breadcrumb->setTitlePostfix($seo['alias']);

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $seo['uri']);
            }

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields($seo['uri']));

            $this->view->assign('form', array_merge(['uri' => $seo['uri']], $_POST));

            $this->secureHelper->generateFormToken($this->request->getQuery());
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
        try {
            $this->seoValidator->validate($formData);

            $bool = $this->seo->insertUriAlias(
                $formData['uri'],
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param $path
     */
    protected function _editPost(array $formData, $path)
    {
        try {
            $this->seoValidator->validate($formData, $path);

            $updateValues = [
                'uri' => $formData['uri'],
                'alias' => $formData['alias'],
                'keywords' => Core\Functions::strEncode($formData['seo_keywords']),
                'description' => Core\Functions::strEncode($formData['seo_description']),
                'robots' => (int)$formData['seo_robots']
            ];

            $bool = $this->seoModel->update($updateValues, $this->request->id);

            $this->seoCache->setCache();

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
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
        $this->view->assign('mod_rewrite', $this->get('core.helpers.forms')->selectGenerator('mod_rewrite', [1, 0], $lang_modRewrite, $seoSettings['mod_rewrite'], 'checked'));

        $this->view->assign('form', array_merge($seoSettings, $_POST));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        try {
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

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}