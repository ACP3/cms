<?php

namespace ACP3\Modules\ACP3\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\CommentRepository
     */
    protected $commentRepository;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validator
     */
    protected $commentsValidator;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext          $context
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Modules\ACP3\Comments\Model\CommentRepository $commentRepository
     * @param \ACP3\Modules\ACP3\Comments\Validator               $commentsValidator
     * @param \ACP3\Core\Helpers\FormToken                        $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Comments\Model\CommentRepository $commentRepository,
        Comments\Validator $commentsValidator,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->commentRepository = $commentRepository;
        $this->commentsValidator = $commentsValidator;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->commentRepository->delete($item, 'module_id');
                }

                return $bool;
            }
        );
    }

    public function actionIndex()
    {
        $comments = $this->commentRepository->getCommentsGroupedByModule();
        $c_comments = count($comments);

        if ($c_comments > 0) {
            $canDelete = $this->acl->hasPermission('admin/comments/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->user->getEntriesPerPage()
            ];
            $this->view->assign('datatable_config', $config);

            for ($i = 0; $i < $c_comments; ++$i) {
                $comments[$i]['name'] = $this->lang->t($comments[$i]['module'], $comments[$i]['module']);
            }
            $this->view->assign('comments', $comments);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('comments');

        $this->view->assign('dateformat', $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']));

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('emoticons', $settings['emoticons']));
        }

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->commentsValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'emoticons' => $formData['emoticons'],
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'comments');
        });
    }
}
