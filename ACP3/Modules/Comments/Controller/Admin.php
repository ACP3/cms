<?php

namespace ACP3\Modules\Comments\Controller;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Description of CommentsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Model
     */
    protected $model;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);

        $this->model = new Comments\Model($this->db, $this->lang, $this->auth, $this->date);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/comments/delete', 'acp/comments');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item, 'module_id');
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionDeleteComments()
    {
        $items = $this->_deleteItem('acp/comments/delete_comments', 'acp/comments');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item);
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $comment = $this->model->getOneById((int)$this->uri->id);

        if (empty($comment) === false) {
            $this->breadcrumb
                ->append($this->lang->t($comment['module'], $comment['module']), $this->uri->route('acp/comments/list_comments/id_' . $comment['module_id']))
                ->append($this->lang->t('comments', 'acp_edit'));

            if (isset($_POST['submit']) === true) {
                try {
                    $this->model->validateEdit($_POST);

                    $update_values = array();
                    $update_values['message'] = Core\Functions::strEncode($_POST['message']);
                    if ((empty($comment['user_id']) || Core\Validate::isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
                        $update_values['name'] = Core\Functions::strEncode($_POST['name']);
                    }

                    $bool = $this->model->update($update_values, $this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/list_comments/id_' . $comment['module_id']);
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

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $comment);
            $this->view->assign('module_id', (int)$comment['module_id']);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $comments = $this->model->getCommentsGroupedByModule();
        $c_comments = count($comments);

        if ($c_comments > 0) {
            $can_delete = Core\Modules::hasPermission('comments', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));
            for ($i = 0; $i < $c_comments; ++$i) {
                $comments[$i]['name'] = $this->lang->t($comments[$i]['module'], $comments[$i]['module']);
            }
            $this->view->assign('comments', $comments);
            $this->view->assign('can_delete', $can_delete);
        }
    }

    public function actionListComments()
    {
        Core\Functions::getRedirectMessage();

        $comments = $this->model->getAllByModuleInAcp((int)$this->uri->id);

        if (empty($comments) === false) {
            $module = $this->db->fetchColumn('SELECT name FROM ' . DB_PRE . 'modules WHERE id = ?', array($this->uri->id));

            //Brotkrümelspur
            $this->breadcrumb->append($this->lang->t($module, $module));

            $c_comments = count($comments);

            if ($c_comments > 0) {
                $can_delete = Core\Modules::hasPermission('comments', 'acp_delete_comments');
                $config = array(
                    'element' => '#acp-table',
                    'sort_col' => $can_delete === true ? 5 : 4,
                    'sort_dir' => 'asc',
                    'hide_col_sort' => $can_delete === true ? 0 : ''
                );
                $this->view->appendContent(Core\Functions::dataTable($config));

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
                $this->view->assign('can_delete', $can_delete);
            }
        }
    }

    public function actionSettings()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateSettings($_POST);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'emoticons' => $_POST['emoticons'],
                );
                $bool = Core\Config::setSettings('comments', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/comments');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = Core\Config::getSettings('comments');

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        // Emoticons erlauben
        if (Core\Modules::isActive('emoticons') === true) {
            $lang_allow_emoticons = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
        }

        $this->session->generateFormToken();
    }

}
