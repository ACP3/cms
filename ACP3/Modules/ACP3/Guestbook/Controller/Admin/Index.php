<?php

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository
     */
    protected $guestbookRepository;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validator
     */
    protected $guestbookValidator;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext             $context
     * @param \ACP3\Core\Date                                        $date
     * @param \ACP3\Core\Helpers\FormToken                           $formTokenHelper
     * @param \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository $guestbookRepository
     * @param \ACP3\Modules\ACP3\Guestbook\Validator                 $guestbookValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Guestbook\Model\GuestbookRepository $guestbookRepository,
        Guestbook\Validator $guestbookValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->guestbookRepository = $guestbookRepository;
        $this->guestbookValidator = $guestbookValidator;
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
                    $bool = $this->guestbookRepository->delete($item);
                }

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $guestbook = $this->guestbookRepository->getOneById($id);
        if (empty($guestbook) === false) {
            $settings = $this->config->getSettings('guestbook');

            $this->breadcrumb->setTitlePostfix($guestbook['name']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $settings, $id);
            }

            if ($settings['emoticons'] == 1 && $this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            if ($settings['notify'] == 2) {
                $this->view->assign('activate', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('active', $guestbook['active']));
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'form' => array_merge($guestbook, $this->request->getPost()->all())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $guestbook = $this->guestbookRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($guestbook)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/guestbook/index/delete')
            ->setResourcePathEdit('admin/guestbook/index/edit');

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
                'label' => $this->lang->t('guestbook', 'ip'),
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
            'show_mass_delete_button' => count($guestbook) > 0
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

        $settings = $this->config->getSettings('guestbook');

        $lang_notify = [
            $this->lang->t('guestbook', 'no_notification'),
            $this->lang->t('guestbook', 'notify_on_new_entry'),
            $this->lang->t('guestbook', 'notify_and_enable')
        ];

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('emoticons', $settings['emoticons']));
        }

        // In Newsletter integrieren
        if ($this->modules->isActive('newsletter') === true) {
            $this->view->assign('newsletter_integration', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('newsletter_integration', $settings['newsletter_integration']));
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'notify' => $this->get('core.helpers.forms')->selectGenerator('notify', [0, 1, 2], $lang_notify, $settings['notify']),
            'overlay' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'form' => array_merge(['notify_email' => $settings['notify_email']], $this->request->getPost()->all())
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, array $settings, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $id) {
            $this->guestbookValidator->validateEdit($formData, $settings);

            $updateValues = [
                'message' => Core\Functions::strEncode($formData['message']),
                'active' => $settings['notify'] == 2 ? $formData['active'] : 1,
            ];

            $bool = $this->guestbookRepository->update($updateValues, $id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->guestbookValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'notify' => $formData['notify'],
                'notify_email' => $formData['notify_email'],
                'overlay' => $formData['overlay'],
                'emoticons' => $formData['emoticons'],
                'newsletter_integration' => $formData['newsletter_integration'],
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'guestbook');
        });
    }
}
