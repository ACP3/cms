<?php

namespace ACP3\Modules\Comments\Controller;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Description of CommentsFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{

    /**
     * @var string
     */
    protected $module;
    /**
     * @var int
     */
    protected $entryId;
    /**
     *
     * @var Comments\Model
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
        Core\View $view,
        Core\SEO $seo,
        $module,
        $entryId)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view, $seo);

        $this->module = $module;
        $this->entryId = $entryId;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Comments\Model($this->db);
    }

    public function actionCreate()
    {
        // Formular für das Eintragen von Kommentaren
        if (empty($_POST) === false) {
            try {
                $ip = $_SERVER['REMOTE_ADDR'];

                $validator = $this->get('comments.validator');
                $validator->validateCreate($_POST, $ip);

                $moduleInfo = $this->modules->getModuleInfo($this->module);
                $insertValues = array(
                    'id' => '',
                    'date' => $this->date->toSQL(),
                    'ip' => $ip,
                    'name' => Core\Functions::strEncode($_POST['name']),
                    'user_id' => $this->auth->isUser() === true && $this->get('core.validate')->isNumber($this->auth->getUserId() === true) ? $this->auth->getUserId() : '',
                    'message' => Core\Functions::strEncode($_POST['message']),
                    'module_id' => $moduleInfo['id'],
                    'entry_id' => $this->entryId,
                );

                $bool = $this->model->insert($insertValues);

                $this->session->unsetFormToken();

                $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                $redirect->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->uri->query);
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
                $redirect->setMessage(false, $e->getMessage(), $this->uri->query);
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $config = new Core\Config($this->db, 'comments');
        $settings = $config->getSettings();

        // Emoticons einbinden, falls diese aktiv sind
        if ($settings['emoticons'] == 1 && $this->modules->isActive('emoticons') === true) {
            // Emoticons im Formular anzeigen
            $this->view->assign('emoticons', $this->get('emoticons.helpers')->emoticonsList());
        }

        $defaults = array(
            'name' => '',
            'name_disabled' => '',
            'message' => ''
        );

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $user = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly" class="readonly"';
            $defaults['name'] = $user['nickname'];
            $defaults['name_disabled'] = $disabled;
            $defaults['message'] = '';
        }

        $this->view->assign('form', array_merge($defaults, $_POST));

        if ($this->modules->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
        }

        $this->session->generateFormToken();

        return $this->view->fetchTemplate('comments/index.create.tpl');
    }

    public function actionIndex()
    {
        $redirect = new Core\Helpers\RedirectMessages($this->uri, $this->view);
        $redirect->getMessage();

        $config = new Core\Config($this->db, 'comments');
        $settings = $config->getSettings();

        // Auflistung der Kommentare
        $comments = $this->model->getAllByModule($this->module, $this->entryId, POS, $this->auth->entries);
        $c_comments = count($comments);

        if ($c_comments > 0) {
            // Falls in den Moduleinstellungen aktiviert und Emoticons überhaupt aktiv sind, diese einbinden
            $emoticonsActive = false;
            if ($settings['emoticons'] == 1) {
                $emoticonsActive = $this->modules->isActive('emoticons');
            }

            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $this->model->countAllByModule($this->module, $this->entryId)
            );
            $pagination->display();

            $formatter = $this->get('core.helpers.string.formatter');
            for ($i = 0; $i < $c_comments; ++$i) {
                if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
                    $comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
                    $comments[$i]['user_id'] = 0;
                }
                $comments[$i]['name'] = !empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'];
                $comments[$i]['date_formatted'] = $this->date->format($comments[$i]['date'], $settings['dateformat']);
                $comments[$i]['date_iso'] = $this->date->format($comments[$i]['date'], 'c');
                $comments[$i]['message'] = $formatter->nl2p($comments[$i]['message']);
                if ($emoticonsActive === true) {
                    $comments[$i]['message'] = $this->get('emoticons.helpers')->emoticonsReplace($comments[$i]['message']);
                }
            }
            $this->view->assign('comments', $comments);
        }

        if ($this->modules->hasPermission('frontend/comments/index/create') === true) {
            $this->view->assign('comments_create_form', $this->actionCreate());
        }

        return $this->view->fetchTemplate('comments/index.index.tpl');
    }

}
