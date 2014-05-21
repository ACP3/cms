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

    protected function _init()
    {
        $this->model = new Comments\Model($this->db, $this->lang, $this->auth, $this->date);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/comments/details/delete', 'acp/comments');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item);
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/index/404');
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
                    $this->model->validateEdit($_POST);

                    $update_values = array();
                    $update_values['message'] = Core\Functions::strEncode($_POST['message']);
                    if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
                        $update_values['name'] = Core\Functions::strEncode($_POST['name']);
                    }

                    $bool = $this->model->update($update_values, $this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/details/index/id_' . $comment['module_id']);
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/comments');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            if (Core\Modules::isActive('emoticons') === true) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
            }

            $this->view->assign('form', array_merge($comment, $_POST));
            $this->view->assign('module_id', (int)$comment['module_id']);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/index/404');
        }
    }

    public function actionIndex()
    {
        Core\Functions::getRedirectMessage();

        $comments = $this->model->getAllByModuleInAcp((int)$this->uri->id);

        if (empty($comments) === false) {
            $module = $this->db->fetchColumn('SELECT name FROM ' . DB_PRE . 'modules WHERE id = ?', array($this->uri->id));

            //Brotkrümelspur
            $this->breadcrumb->append($this->lang->t($module, $module));

            $c_comments = count($comments);

            if ($c_comments > 0) {
                $canDelete = Core\Modules::hasPermission('admin/comments/details/delete');
                $config = array(
                    'element' => '#acp-table',
                    'sort_col' => $canDelete === true ? 5 : 4,
                    'sort_dir' => 'asc',
                    'hide_col_sort' => $canDelete === true ? 0 : ''
                );
                $this->appendContent(Core\Functions::dataTable($config));

                $settings = Core\Config::getSettings('comments');
                // Emoticons einbinden
                $emoticons_active = false;
                if ($settings['emoticons'] == 1) {
                    if (Core\Modules::isActive('emoticons') === true) {
                        $emoticons_active = true;
                    }
                }

                for ($i = 0; $i < $c_comments; ++$i) {
                    if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
                        $comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
                    }
                    $comments[$i]['date_formatted'] = $this->date->formatTimeRange($comments[$i]['date']);
                    $comments[$i]['message'] = Core\Functions::nl2p($comments[$i]['message']);
                    if ($emoticons_active === true) {
                        $comments[$i]['message'] = \ACP3\Modules\Emoticons\Helpers::emoticonsReplace($comments[$i]['message']);
                    }
                }
                $this->view->assign('comments', $comments);
                $this->view->assign('can_delete', $canDelete);
            }
        }
    }

}
