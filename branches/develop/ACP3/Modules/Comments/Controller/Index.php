<?php

namespace ACP3\Modules\Comments\Controller;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Class Index
 * @package ACP3\Modules\Comments\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
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

    /**
     * @param Core\Context\Frontend $context
     * @param Core\Date $date
     * @param Core\Pagination $pagination
     * @param Comments\Model $commentsModel
     * @param Core\Config $commentsConfig
     * @param Core\Helpers\Secure $secureHelper
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Comments\Model $commentsModel,
        Core\Config $commentsConfig,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->commentsModel = $commentsModel;
        $this->commentsConfig = $commentsConfig;
        $this->secureHelper = $secureHelper;
    }

    /**
     * @param $module
     *
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @param $entryId
     *
     * @return $this
     */
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;

        return $this;
    }

    public function actionIndex()
    {
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

            $this->pagination->setTotalResults($this->commentsModel->countAllByModule($this->module, $this->entryId));
            $this->pagination->display();

            for ($i = 0; $i < $c_comments; ++$i) {
                if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
                    $comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
                    $comments[$i]['user_id'] = 0;
                }
                $comments[$i]['name'] = !empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'];
                if ($emoticonsActive === true) {
                    $comments[$i]['message'] = $this->get('emoticons.helpers')->emoticonsReplace($comments[$i]['message']);
                }
            }
            $this->view->assign('comments', $comments);
            $this->view->assign('dateformat', $settings['dateformat']);
        }

        if ($this->acl->hasPermission('frontend/comments/index/create') === true) {
            $this->view->assign('comments_create_form', $this->actionCreate());
        }

        return $this->view->fetchTemplate('Comments/Frontend/index.index.tpl');
    }

    public function actionCreate()
    {
        // Formular für das Eintragen von Kommentaren
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        $settings = $this->commentsConfig->getSettings();

        // Emoticons einbinden, falls diese aktiv sind
        if ($settings['emoticons'] == 1 && $this->modules->isActive('emoticons') === true) {
            // Emoticons im Formular anzeigen
            $this->view->assign('emoticons', $this->get('emoticons.helpers')->emoticonsList());
        }

        $defaults = [
            'name' => '',
            'name_disabled' => '',
            'message' => ''
        ];

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $user = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly"';
            $defaults['name'] = $user['nickname'];
            $defaults['name_disabled'] = $disabled;
            $defaults['message'] = '';
        }

        $this->view->assign('form', array_merge($defaults, $_POST));

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
        }

        $this->secureHelper->generateFormToken($this->request->query);

        return $this->view->fetchTemplate('Comments/Frontend/index.create.tpl');
    }

    /**
     * @param array $formData
     */
    private function _createPost(array $formData)
    {
        try {
            $ip = $_SERVER['REMOTE_ADDR'];

            $validator = $this->get('comments.validator');
            $validator->validateCreate($formData, $ip);

            $moduleInfo = $this->modules->getModuleInfo($this->module);
            $insertValues = [
                'id' => '',
                'date' => $this->date->toSQL(),
                'ip' => $ip,
                'name' => Core\Functions::strEncode($formData['name']),
                'user_id' => $this->auth->isUser() === true && $this->get('core.validator.rules.misc')->isNumber($this->auth->getUserId() === true) ? $this->auth->getUserId() : '',
                'message' => Core\Functions::strEncode($formData['message']),
                'module_id' => $moduleInfo['id'],
                'entry_id' => $this->entryId,
            ];

            $bool = $this->commentsModel->insert($insertValues);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->request->query);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), $this->request->query);
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
