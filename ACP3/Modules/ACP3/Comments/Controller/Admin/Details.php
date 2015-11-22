<?php

namespace ACP3\Modules\ACP3\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\System;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin
 */
class Details extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\CommentRepository
     */
    protected $commentRepository;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validator
     */
    protected $commentsValidator;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\ModuleRepository
     */
    protected $systemModuleRepository;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext          $context
     * @param \ACP3\Modules\ACP3\Comments\Model\CommentRepository $commentRepository
     * @param \ACP3\Modules\ACP3\Comments\Validator               $commentsValidator
     * @param \ACP3\Modules\ACP3\System\Model\ModuleRepository    $systemModuleRepository
     * @param \ACP3\Core\Helpers\FormToken                        $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Comments\Model\CommentRepository $commentRepository,
        Comments\Validator $commentsValidator,
        System\Model\ModuleRepository $systemModuleRepository,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
        $this->commentsValidator = $commentsValidator;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->formTokenHelper = $formTokenHelper;
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
     * @param int    $id
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($id, $action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $this,
            $action,
            function ($items) use ($id) {
                $bool = false;

                foreach ($items as $item) {
                    $bool = $this->commentRepository->delete($item);
                }

                // If there are no comments for the given module, redirect to the general comments admin panel page
                if ($this->commentRepository->countAll($id) == 0) {
                    return $this->redirectMessages()->setMessage(
                        $bool,
                        $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'),
                        'acp/comments'
                    );
                }

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'),
                    'acp/comments/details/index/id_' . $id
                );
            },
            'acp/comments/details/delete/id_' . $id,
            'acp/comments/details/index/id_' . $id
        );
    }

    /**
     * @param $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $comment = $this->commentRepository->getOneById($id);

        if (empty($comment) === false) {
            $this->breadcrumb
                ->append($this->lang->t($comment['module'], $comment['module']), 'acp/comments/details/index/id_' . $comment['module_id'])
                ->append($this->lang->t('comments', 'admin_details_edit'))
                ->setTitlePostfix($comment['name']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $comment, $id);
            }

            if ($this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'form' => array_merge($comment, $this->request->getPost()->all()),
                'module_id' => (int)$comment['module_id']
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionIndex($id)
    {
        $comments = $this->commentRepository->getAllByModuleInAcp($id);

        if (empty($comments) === false) {
            $moduleName = $this->systemModuleRepository->getModuleNameById($id);

            //Brotkrümelspur
            $this->breadcrumb->append($this->lang->t($moduleName, $moduleName));

            /** @var Core\Helpers\DataGrid $dataGrid */
            $dataGrid = $this->get('core.helpers.data_grid');
            $dataGrid
                ->setResults($comments)
                ->setRecordsPerPage($this->user->getEntriesPerPage())
                ->setIdentifier('#acp-table')
                ->setResourcePathDelete('admin/comments/details/delete/id_' . $id)
                ->setResourcePathEdit('admin/comments/details/edit');

            $dataGrid
                ->addColumn([
                    'label' => $this->lang->t('system', 'date'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::NAME,
                    'fields' => ['date'],
                    'default_sort' => true
                ], 50)
                ->addColumn([
                    'label' => $this->lang->t('system', 'name'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                    'fields' => ['name'],
                ], 40)
                ->addColumn([
                    'label' => $this->lang->t('system', 'message'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\Nl2pColumnRenderer::NAME,
                    'fields' => ['message'],
                ], 30)
                ->addColumn([
                    'label' => $this->lang->t('comments', 'ip'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                    'fields' => ['ip'],
                ], 20)
                ->addColumn([
                    'label' => $this->lang->t('system', 'id'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::NAME,
                    'fields' => ['id'],
                    'primary' => true
                ], 10);

            return [
                'grid' => $dataGrid->render(),
                'show_mass_delete_button' => count($comments) > 0
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $comment
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, array $comment, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $comment, $id) {
            $this->commentsValidator->validateEdit($formData);

            $updateValues = [];
            $updateValues['message'] = Core\Functions::strEncode($formData['message']);
            if ((empty($comment['user_id']) || $this->get('core.validator.rules.misc')->isNumber($comment['user_id']) === false) && !empty($formData['name'])) {
                $updateValues['name'] = Core\Functions::strEncode($formData['name']);
            }

            $bool = $this->commentRepository->update($updateValues, $id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
