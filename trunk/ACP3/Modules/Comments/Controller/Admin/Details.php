<?php

namespace ACP3\Modules\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Description of CommentsAdmin
 *
 * @author Tino Goratsch
 */
class Details extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Comments\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Comments\Model($this->db);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/comments/details/delete', 'acp/comments');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item);
            }

            $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
            $redirect->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $comment = $this->model->getOneById((int)$this->uri->id);

        if (empty($comment) === false) {
            $this->breadcrumb
                ->append($this->lang->t($comment['module'], $comment['module']), 'acp/comments/details/index/id_' . $comment['module_id'])
                ->append($this->lang->t('comments', 'admin_details_edit'));

            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('comments.validator');
                    $validator->validateEdit($_POST);

                    $update_values = array();
                    $update_values['message'] = Core\Functions::strEncode($_POST['message']);
                    if ((empty($comment['user_id']) || $this->get('core.validate')->isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
                        $update_values['name'] = Core\Functions::strEncode($_POST['name']);
                    }

                    $bool = $this->model->update($update_values, $this->uri->id);

                    $this->session->unsetFormToken();

                    $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                    $redirect->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/details/index/id_' . $comment['module_id']);
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                    $redirect->setMessage(false, $e->getMessage(), 'acp/comments');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            if ($this->modules->isActive('emoticons') === true) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->get('emoticons.helpers')->emoticonsList());
            }

            $this->view->assign('form', array_merge($comment, $_POST));
            $this->view->assign('module_id', (int)$comment['module_id']);

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
        $redirect->getMessage();

        $comments = $this->model->getAllByModuleInAcp((int)$this->uri->id);

        if (empty($comments) === false) {
            $module = $this->db->fetchColumn('SELECT name FROM ' . DB_PRE . 'modules WHERE id = ?', array($this->uri->id));

            //Brotkrümelspur
            $this->breadcrumb->append($this->lang->t($module, $module));

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

                $config = new Core\Config($this->db, 'comments');
                $settings = $config->getSettings();

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
