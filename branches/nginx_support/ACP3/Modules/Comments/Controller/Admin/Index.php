<?php

namespace ACP3\Modules\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Class Index
 * @package ACP3\Modules\Comments\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var Comments\Model
     */
    protected $commentsModel;
    /**
     * @var \ACP3\Core\Config
     */
    protected $commentsConfig;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;

    /**
     * @param Core\Context\Admin $context
     * @param Core\Date $date
     * @param Comments\Model $commentsModel
     * @param Core\Config $commentsConfig
     * @param Core\Helpers\Secure $secureHelper
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Comments\Model $commentsModel,
        Core\Config $commentsConfig,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->commentsModel = $commentsModel;
        $this->commentsConfig = $commentsConfig;
        $this->secureHelper = $secureHelper;
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/comments/index/delete', 'acp/comments');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->commentsModel->delete($item, 'module_id');
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $comments = $this->commentsModel->getCommentsGroupedByModule();
        $c_comments = count($comments);

        if ($c_comments > 0) {
            $canDelete = $this->acl->hasPermission('admin/comments/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);

            for ($i = 0; $i < $c_comments; ++$i) {
                $comments[$i]['name'] = $this->lang->t($comments[$i]['module'], $comments[$i]['module']);
            }
            $this->view->assign('comments', $comments);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
        }

        $settings = $this->commentsConfig->getSettings();

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $lang_allowEmoticons = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->selectGenerator('emoticons', [1, 0], $lang_allowEmoticons, $settings['emoticons'], 'checked'));
        }

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     */
    private function _settingsPost(array $formData)
    {
        try {
            $validator = $this->get('comments.validator');
            $validator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'emoticons' => $formData['emoticons'],
            ];
            $bool = $this->commentsConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/comments');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
