<?php

namespace ACP3\Modules\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Comments;
use ACP3\Modules\System;

/**
 * Class Details
 * @package ACP3\Modules\Comments\Controller\Admin
 */
class Details extends Core\Modules\Controller\Admin
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
     * @var \ACP3\Modules\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;

    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Comments\Model $commentsModel,
        Core\Config $commentsConfig,
        System\Model $systemModel,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->commentsModel = $commentsModel;
        $this->commentsConfig = $commentsConfig;
        $this->systemModel = $systemModel;
        $this->secureHelper = $secureHelper;
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
                try {
                    $validator = $this->get('comments.validator');
                    $validator->validateEdit($_POST);

                    $updateValues = array();
                    $updateValues['message'] = Core\Functions::strEncode($_POST['message']);
                    if ((empty($comment['user_id']) || $this->get('core.validator.rules.misc')->isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
                        $updateValues['name'] = Core\Functions::strEncode($_POST['name']);
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

            if ($this->modules->isActive('emoticons') === true) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->get('emoticons.helpers')->emoticonsList());
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
        $this->redirectMessages()->getMessage();

        $comments = $this->commentsModel->getAllByModuleInAcp((int)$this->request->id);

        if (empty($comments) === false) {
            $moduleName = $this->systemModel->getModuleNameById($this->request->id);

            //Brotkrümelspur
            $this->breadcrumb->append($this->lang->t($moduleName, $moduleName));

            $c_comments = count($comments);

            if ($c_comments > 0) {
                $canDelete = $this->modules->hasPermission('admin/comments/details/delete');
                $config = array(
                    'element' => '#acp-table',
                    'sort_col' => $canDelete === true ? 5 : 4,
                    'sort_dir' => 'asc',
                    'hide_col_sort' => $canDelete === true ? 0 : ''
                );
                $this->appendContent($this->get('core.functions')->dataTable($config));

                $settings = $this->commentsConfig->getSettings();

                // Emoticons einbinden
                $emoticons_active = false;
                if ($settings['emoticons'] == 1) {
                    if ($this->modules->isActive('emoticons') === true) {
                        $emoticons_active = true;
                    }
                }

                $formatter = $this->get('core.helpers.string.formatter');
                for ($i = 0; $i < $c_comments; ++$i) {
                    if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
                        $comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
                    }
                    $comments[$i]['date_formatted'] = $this->date->formatTimeRange($comments[$i]['date']);
                    $comments[$i]['message'] = $formatter->nl2p($comments[$i]['message']);
                    if ($emoticons_active === true) {
                        $comments[$i]['message'] = $this->get('emoticons.helpers')->emoticonsReplace($comments[$i]['message']);
                    }
                }
                $this->view->assign('comments', $comments);
                $this->view->assign('can_delete', $canDelete);
            }
        }
    }

}
