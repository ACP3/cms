<?php

namespace ACP3\Modules\Comments\Controller;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Class Index
 * @package ACP3\Modules\Comments\Controller
 */
class Index extends Core\Modules\Controller
{

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Session
     */
    protected $session;
    /**
     * @var string
     */
    protected $module;
    /**
     * @var int
     */
    protected $entryId;
    /**
     * @var Comments\Model
     */
    protected $commentsModel;
    /**
     * @var Core\Config
     */
    protected $commentsConfig;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Date $date,
        Core\Session $session,
        Comments\Model $commentsModel,
        Core\Config $commentsConfig)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

        $this->date = $date;
        $this->session = $session;
        $this->commentsModel = $commentsModel;
        $this->commentsConfig = $commentsConfig;
    }

    /**
     * @param $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @param $entryId
     * @return $this
     */
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;

        return $this;
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

                $bool = $this->commentsModel->insert($insertValues);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->uri->query);
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), $this->uri->query);
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $settings = $this->commentsConfig->getSettings();

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
        $this->redirectMessages()->getMessage();

        $settings = $this->commentsConfig->getSettings();

        // Auflistung der Kommentare
        $comments = $this->commentsModel->getAllByModule($this->module, $this->entryId, POS, $this->auth->entries);
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
                $this->commentsModel->countAllByModule($this->module, $this->entryId)
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