<?php

namespace ACP3\Modules\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Comments;
use ACP3\Modules\Emoticons;
use ACP3\Modules\System;

/**
 * Class Details
 * @package ACP3\Modules\Comments\Controller\Admin
 */
class Details extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Modules\Comments\Model
     */
    protected $commentsModel;
    /**
     * @var \ACP3\Modules\Comments\Validator
     */
    protected $commentsValidator;
    /**
     * @var \ACP3\Core\Config
     */
    protected $commentsConfig;
    /**
     * @var \ACP3\Modules\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * @param \ACP3\Core\Context\Admin         $context
     * @param \ACP3\Modules\Comments\Model     $commentsModel
     * @param \ACP3\Modules\Comments\Validator $commentsValidator
     * @param \ACP3\Core\Config                $commentsConfig
     * @param \ACP3\Modules\System\Model       $systemModel
     * @param \ACP3\Core\Helpers\Secure        $secureHelper
     */
    public function __construct(
        Core\Context\Admin $context,
        Comments\Model $commentsModel,
        Comments\Validator $commentsValidator,
        Core\Config $commentsConfig,
        System\Model $systemModel,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->commentsModel = $commentsModel;
        $this->commentsValidator = $commentsValidator;
        $this->commentsConfig = $commentsConfig;
        $this->systemModel = $systemModel;
        $this->secureHelper = $secureHelper;
    }

    /**
     * @param \ACP3\Modules\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/comments/details/delete', 'acp/comments');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->commentsModel->delete($item);
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $comment = $this->commentsModel->getOneById((int)$this->request->id);

        if (empty($comment) === false) {
            $this->breadcrumb
                ->append($this->lang->t($comment['module'], $comment['module']), 'acp/comments/details/index/id_' . $comment['module_id'])
                ->append($this->lang->t('comments', 'admin_details_edit'));

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $comment);
            }

            if ($this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            $this->view->assign('form', array_merge($comment, $_POST));
            $this->view->assign('module_id', (int)$comment['module_id']);

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $comments = $this->commentsModel->getAllByModuleInAcp((int)$this->request->id);

        if (empty($comments) === false) {
            $moduleName = $this->systemModel->getModuleNameById($this->request->id);

            //Brotkrümelspur
            $this->breadcrumb->append($this->lang->t($moduleName, $moduleName));

            $c_comments = count($comments);

            if ($c_comments > 0) {
                $canDelete = $this->acl->hasPermission('admin/comments/details/delete');
                $config = [
                    'element' => '#acp-table',
                    'sort_col' => $canDelete === true ? 5 : 4,
                    'sort_dir' => 'asc',
                    'hide_col_sort' => $canDelete === true ? 0 : '',
                    'records_per_page' => $this->auth->entries
                ];
                $this->view->assign('datatable_config', $config);

                $settings = $this->commentsConfig->getSettings();

                // Emoticons einbinden
                $emoticonsActive = ($settings['emoticons'] == 1 && $this->emoticonsHelpers);

                for ($i = 0; $i < $c_comments; ++$i) {
                    if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
                        $comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
                    }
                    if ($emoticonsActive === true) {
                        $comments[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($comments[$i]['message']);
                    }
                }
                $this->view->assign('comments', $comments);
                $this->view->assign('can_delete', $canDelete);
            }
        }
    }

    /**
     * @param array $formData
     * @param array $comment
     */
    private function _editPost(array $formData, array $comment)
    {
        try {
            $this->commentsValidator->validateEdit($formData);

            $updateValues = [];
            $updateValues['message'] = Core\Functions::strEncode($formData['message']);
            if ((empty($comment['user_id']) || $this->get('core.validator.rules.misc')->isNumber($comment['user_id']) === false) && !empty($formData['name'])) {
                $updateValues['name'] = Core\Functions::strEncode($formData['name']);
            }

            $bool = $this->commentsModel->update($updateValues, $this->request->id);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/details/index/id_' . $comment['module_id']);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/comments');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
