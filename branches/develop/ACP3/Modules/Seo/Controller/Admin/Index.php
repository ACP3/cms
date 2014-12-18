<?php

namespace ACP3\Modules\Seo\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Seo;

/**
 * Class Index
 * @package ACP3\Modules\Seo\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var Core\Config
     */
    protected $seoConfig;
    /**
     * @var Seo\Model
     */
    protected $seoModel;

    /**
     * @param \ACP3\Core\Context\Admin  $context
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Modules\Seo\Cache   $seoCache
     * @param \ACP3\Core\Config         $seoConfig
     * @param \ACP3\Modules\Seo\Model   $seoModel
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Helpers\Secure $secureHelper,
        Seo\Cache $seoCache,
        Core\Config $seoConfig,
        Seo\Model $seoModel)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->seoCache = $seoCache;
        $this->seoConfig = $seoConfig;
        $this->seoModel = $seoModel;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge(['uri' => ''], $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/seo/index/delete', 'acp/seo');

        if ($this->request->action === 'confirmed') {
            $bool = false;

            foreach ($items as $item) {
                $bool = $this->seoModel->delete($item);
            }

            $this->seo->setCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/seo');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $seo = $this->seoModel->getOneById((int)$this->request->id);

        if (empty($seo) === false) {
            if (empty($_POST) === false) {
                $this->_editPost($_POST, $seo['uri']);
            }

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields($seo['uri']));

            $this->view->assign('form', array_merge(['uri' => $seo['uri']], $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
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
    private function _createPost(array $formData)
    {
        try {
            $this->get('seo.validator')->validate($formData);

            $bool = $this->seo->insertUriAlias(
                $formData['uri'],
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/seo');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/seo');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param $path
     */
    private function _editPost(array $formData, $path)
    {
        try {
            $this->get('seo.validator')->validate($formData, $path);

            $updateValues = [
                'uri' => $formData['uri'],
                'alias' => $formData['alias'],
                'keywords' => Core\Functions::strEncode($formData['seo_keywords']),
                'description' => Core\Functions::strEncode($formData['seo_description']),
                'robots' => (int)$formData['seo_robots']
            ];

            $bool = $this->seoModel->update($updateValues, $this->request->id);

            $this->seoCache->setCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/seo');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/seo');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
        }

        $seoSettings = $this->seoConfig->getSettings();

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

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     */
    private function _settingsPost(array $formData)
    {
        try {
            $this->get('seo.validator')->validateSettings($formData);

            // Config aktualisieren
            $data = [
                'meta_description' => Core\Functions::strEncode($formData['meta_description']),
                'meta_keywords' => Core\Functions::strEncode($formData['meta_keywords']),
                'mod_rewrite' => (int)$formData['mod_rewrite'],
                'robots' => (int)$formData['robots'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $bool = $this->seoConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'), 'acp/seo');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/seo');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}