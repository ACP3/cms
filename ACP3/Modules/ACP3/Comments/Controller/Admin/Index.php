<?php

namespace ACP3\Modules\ACP3\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model
     */
    protected $commentsModel;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validator
     */
    protected $commentsValidator;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $formTokenHelper;

    /**
     * @param \ACP3\Core\Context\Admin              $context
     * @param \ACP3\Core\Date                       $date
     * @param \ACP3\Modules\ACP3\Comments\Model     $commentsModel
     * @param \ACP3\Modules\ACP3\Comments\Validator $commentsValidator
     * @param \ACP3\Core\Helpers\FormToken          $formTokenHelper
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Comments\Model $commentsModel,
        Comments\Validator $commentsValidator,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->commentsModel = $commentsModel;
        $this->commentsValidator = $commentsValidator;
        $this->formTokenHelper = $formTokenHelper;
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->getParameters()->get('action') === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->commentsModel->delete($item, 'module_id');
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
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
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll());
        }

        $settings = $this->config->getSettings('comments');

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $lang_allowEmoticons = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->selectGenerator('emoticons', [1, 0], $lang_allowEmoticons, $settings['emoticons'], 'checked'));
        }

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        try {
            $this->commentsValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'emoticons' => $formData['emoticons'],
            ];
            $bool = $this->config->setSettings($data, 'comments');

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
