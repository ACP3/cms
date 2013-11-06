<?php

namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Description of CommentsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    private $module;
    private $entryId;

    /**
     *
     * @var Model
     */
    private $model;

    public function __construct($module, $entry_id)
    {
        parent::__construct();

        $this->module = $module;
        $this->entryId = $entry_id;
        $this->model = new Model($this->db);
    }

    public function actionCreate()
    {
        // Formular für das Eintragen von Kommentaren
        if (isset($_POST['submit']) === true) {
            try {
                $ip = $_SERVER['REMOTE_ADDR'];

                $this->model->validateCreate($_POST, $ip, $this->lang, $this->auth, $this->date);

                $mod_id = $this->db->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($this->module));
                $insert_values = array(
                    'id' => '',
                    'date' => $this->date->toSQL(),
                    'ip' => $ip,
                    'name' => Core\Functions::strEncode($_POST['name']),
                    'user_id' => $this->auth->isUser() === true && Core\Validate::isNumber($this->auth->getUserId() === true) ? $this->auth->getUserId() : '',
                    'message' => Core\Functions::strEncode($_POST['message']),
                    'module_id' => $mod_id,
                    'entry_id' => $this->entryId,
                );

                $bool = $this->model->insert($insert_values);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->uri->query);
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), $this->uri->query);
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = Core\Config::getSettings('comments');

        // Emoticons einbinden, falls diese aktiv sind
        if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
            // Emoticons im Formular anzeigen
            $this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
        }

        $defaults = array();

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $user = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly" class="readonly"';

            if (isset($_POST['submit'])) {
                $_POST['name'] = $user['nickname'];
                $_POST['name_disabled'] = $disabled;
            } else {
                $defaults['name'] = $user['nickname'];
                $defaults['name_disabled'] = $disabled;
                $defaults['message'] = '';
            }
        } else {
            $defaults['name'] = '';
            $defaults['name_disabled'] = '';
            $defaults['message'] = '';
        }
        $this->view->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);

        if (Core\Modules::hasPermission('captcha', 'image') === true) {
            $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
        }

        $this->session->generateFormToken();

        return $this->view->fetchTemplate('comments/create.tpl');
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $settings = Core\Config::getSettings('comments');

        // Auflistung der Kommentare
        $comments = $this->model->getAllByModule($this->module, $this->entryId, POS, $this->auth->entries);
        $c_comments = count($comments);

        if ($c_comments > 0) {
            // Falls in den Moduleinstellungen aktiviert und Emoticons überhaupt aktiv sind, diese einbinden
            $emoticonsActive = false;
            if ($settings['emoticons'] == 1) {
                $emoticonsActive = Core\Modules::isActive('emoticons');
            }

            $this->view->assign('pagination', Core\Functions::pagination(Helpers::commentsCount($this->module, $this->entryId)));

            for ($i = 0; $i < $c_comments; ++$i) {
                if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
                    $comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
                    $comments[$i]['user_id'] = 0;
                }
                $comments[$i]['name'] = !empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'];
                $comments[$i]['date_formatted'] = $this->date->format($comments[$i]['date'], $settings['dateformat']);
                $comments[$i]['date_iso'] = $this->date->format($comments[$i]['date'], 'c');
                $comments[$i]['message'] = Core\Functions::nl2p($comments[$i]['message']);
                if ($emoticonsActive === true) {
                    $comments[$i]['message'] = \ACP3\Modules\Emoticons\Helpers::emoticonsReplace($comments[$i]['message']);
                }
            }
            $this->view->assign('comments', $comments);
        }

        if (Core\Modules::hasPermission('comments', 'create') === true) {
            $this->view->assign('comments_create_form', $this->actionCreate());
        }

        return $this->view->fetchTemplate('comments/list.tpl');
    }

}
