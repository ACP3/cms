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
     * @var \ACP3\Modules\ACP3\Comments\Validation\FormValidation
     */
    protected $commentsValidator;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext            $context
     * @param \ACP3\Core\Date                                       $date
     * @param \ACP3\Modules\ACP3\Comments\Model\CommentRepository   $commentRepository
     * @param \ACP3\Modules\ACP3\Comments\Validation\FormValidation $commentsValidator
     * @param \ACP3\Core\Helpers\FormToken                          $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Comments\Model\CommentRepository $commentRepository,
        Comments\Validation\FormValidation $commentsValidator,
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

    /**
     * @return array
     */
    public function actionIndex()
    {
        $comments = $this->commentRepository->getCommentsGroupedByModule();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($comments)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/comments/index/delete')
            ->setResourcePathEdit('admin/comments/details/index');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('comments', 'module'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TranslateColumnRenderer::NAME,
                'fields' => ['module'],
                'default_sort' => true
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('comments', 'comments_count'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::NAME,
                'fields' => ['comments_count'],
            ], 20)
            ->addColumn([
                'fields' => ['module_id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($comments) > 0
        ];
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('comments');

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('emoticons', $settings['emoticons']));
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat'])
        ];
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
