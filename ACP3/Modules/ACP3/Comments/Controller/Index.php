<?php

namespace ACP3\Modules\ACP3\Comments\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Comments\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model
     */
    protected $commentsModel;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validator
     */
    protected $commentsValidator;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Helpers
     */
    protected $captchaHelpers;
    /**
     * @var string
     */
    protected $module;
    /**
     * @var int
     */
    protected $entryId;
    /**
     * @var bool
     */
    protected $emoticonsActive;
    /**
     * @var array
     */
    protected $commentsSettings;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Core\Pagination                         $pagination
     * @param \ACP3\Modules\ACP3\Comments\Model             $commentsModel
     * @param \ACP3\Modules\ACP3\Comments\Validator         $commentsValidator
     * @param \ACP3\Core\Helpers\FormToken                  $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Comments\Model $commentsModel,
        Comments\Validator $commentsValidator,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->commentsModel = $commentsModel;
        $this->commentsValidator = $commentsValidator;
        $this->formTokenHelper = $formTokenHelper;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->commentsSettings = $this->config->getSettings('comments');
        $this->emoticonsActive = ($this->commentsSettings['emoticons'] == 1);
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

    /**
     * @param \ACP3\Modules\ACP3\Captcha\Helpers $captchaHelpers
     *
     * @return $this
     */
    public function setCaptchaHelpers(Captcha\Helpers $captchaHelpers)
    {
        $this->captchaHelpers = $captchaHelpers;

        return $this;
    }

    /**
     * @param string $module
     *
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @param int $entryId
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
        // Auflistung der Kommentare
        $comments = $this->commentsModel->getAllByModule($this->modules->getModuleId($this->module), $this->entryId, POS, $this->auth->entries);
        $c_comments = count($comments);

        if ($c_comments > 0) {
            $this->pagination->setTotalResults($this->commentsModel->countAllByModule($this->modules->getModuleId($this->module), $this->entryId));
            $this->pagination->display();

            for ($i = 0; $i < $c_comments; ++$i) {
                if (empty($comments[$i]['user_name']) && empty($comments[$i]['name'])) {
                    $comments[$i]['name'] = $this->lang->t('users', 'deleted_user');
                    $comments[$i]['user_id'] = 0;
                }
                $comments[$i]['name'] = !empty($comments[$i]['user_name']) ? $comments[$i]['user_name'] : $comments[$i]['name'];
                if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
                    $comments[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($comments[$i]['message']);
                }
            }
            $this->view->assign('comments', $comments);
            $this->view->assign('dateformat', $this->commentsSettings['dateformat']);
        }

        if ($this->acl->hasPermission('frontend/comments/index/create') === true) {
            $this->view->assign('comments_create_form', $this->actionCreate());
        }

        return $this->view->fetchTemplate('Comments/Frontend/index.index.tpl');
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        // Emoticons einbinden, falls diese aktiv sind
        if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
            // Emoticons im Formular anzeigen
            $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
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

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken($this->request->getQuery());

        return $this->view->fetchTemplate('Comments/Frontend/index.create.tpl');
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        $this->handlePostAction(
            function () use ($formData) {
                $ip = $this->request->getServer()->get('REMOTE_ADDR', '');

                $this->commentsValidator->validateCreate($formData, $ip);

                $insertValues = [
                    'id' => '',
                    'date' => $this->date->toSQL(),
                    'ip' => $ip,
                    'name' => Core\Functions::strEncode($formData['name']),
                    'user_id' => $this->auth->isUser() === true && $this->get('core.validator.rules.misc')->isNumber($this->auth->getUserId() === true) ? $this->auth->getUserId() : '',
                    'message' => Core\Functions::strEncode($formData['message']),
                    'module_id' => $this->modules->getModuleId($this->module),
                    'entry_id' => $this->entryId,
                ];

                $bool = $this->commentsModel->insert($insertValues);

                $this->formTokenHelper->unsetFormToken($this->request->getQuery());

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), $this->request->getQuery());
            }
        );
    }
}
