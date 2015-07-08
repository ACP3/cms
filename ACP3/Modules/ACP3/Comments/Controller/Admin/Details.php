<?php

namespace ACP3\Modules\ACP3\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\System;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin
 */
class Details extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model
     */
    protected $commentsModel;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validator
     */
    protected $commentsValidator;
    /**
     * @var \ACP3\Modules\ACP3\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext              $context
     * @param \ACP3\Modules\ACP3\Comments\Model     $commentsModel
     * @param \ACP3\Modules\ACP3\Comments\Validator $commentsValidator
     * @param \ACP3\Modules\ACP3\System\Model       $systemModel
     * @param \ACP3\Core\Helpers\FormToken          $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Comments\Model $commentsModel,
        Comments\Validator $commentsValidator,
        System\Model $systemModel,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->commentsModel = $commentsModel;
        $this->commentsValidator = $commentsValidator;
        $this->systemModel = $systemModel;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers
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
        $items = $this->_deleteItem(null, 'acp/comments');

        if ($this->request->getParameters()->get('action') === 'confirmed') {
            $bool = false;

            // Get the module-ID of the first item
            $moduleId = 0;
            if (isset($items[0])) {
                $comment = $this->commentsModel->getOneById($items[0]);
                if (!empty($comment)) {
                    $moduleId = $comment['module_id'];
                }
            }

            foreach ($items as $item) {
                $bool = $this->commentsModel->delete($item);
            }

            // If there are no comments for the given module, redirect to the general comments admin panel page
            if ($this->commentsModel->countAll($moduleId) == 0) {
                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
            } else {
                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments/details/index/id_' . $moduleId);
            }
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $comment = $this->commentsModel->getOneById((int)$this->request->getParameters()->get('id'));

        if (empty($comment) === false) {
            $this->breadcrumb
                ->append($this->lang->t($comment['module'], $comment['module']), 'acp/comments/details/index/id_' . $comment['module_id'])
                ->append($this->lang->t('comments', 'admin_details_edit'))
                ->setTitlePostfix($comment['name']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $comment);
            }

            if ($this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            $this->view->assign('form', array_merge($comment, $this->request->getPost()->getAll()));
            $this->view->assign('module_id', (int)$comment['module_id']);

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $comments = $this->commentsModel->getAllByModuleInAcp((int)$this->request->getParameters()->get('id'));

        if (empty($comments) === false) {
            $moduleName = $this->systemModel->getModuleNameById($this->request->getParameters()->get('id'));

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

                $settings = $this->config->getSettings('comments');

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
    protected function _editPost(array $formData, array $comment)
    {
        try {
            $this->commentsValidator->validateEdit($formData);

            $updateValues = [];
            $updateValues['message'] = Core\Functions::strEncode($formData['message']);
            if ((empty($comment['user_id']) || $this->get('core.validator.rules.misc')->isNumber($comment['user_id']) === false) && !empty($formData['name'])) {
                $updateValues['name'] = Core\Functions::strEncode($formData['name']);
            }

            $bool = $this->commentsModel->update($updateValues, $this->request->getParameters()->get('id'));

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/details/index/id_' . $comment['module_id']);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/comments/details/index/id_' . $comment['module_id']);
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
