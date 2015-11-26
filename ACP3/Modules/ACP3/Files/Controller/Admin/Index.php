<?php

namespace ACP3\Modules\ACP3\Files\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\Files\Helpers;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Files\Controller\Admin
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
     * @var \ACP3\Modules\ACP3\Files\Model\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    protected $filesCache;
    /**
     * @var \ACP3\Modules\ACP3\Files\Validator
     */
    protected $filesValidator;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    protected $commentsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext     $context
     * @param \ACP3\Core\Date                                $date
     * @param \ACP3\Core\Helpers\FormToken                   $formTokenHelper
     * @param \ACP3\Modules\ACP3\Files\Model\FilesRepository $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache                 $filesCache
     * @param \ACP3\Modules\ACP3\Files\Validator             $filesValidator
     * @param \ACP3\Modules\ACP3\Categories\Helpers          $categoriesHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Model\FilesRepository $filesRepository,
        Files\Cache $filesCache,
        Files\Validator $filesValidator,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
        $this->filesValidator = $filesValidator;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @param \ACP3\Modules\ACP3\Comments\Helpers $commentsHelpers
     *
     * @return $this
     */
    public function setCommentsHelpers(Comments\Helpers $commentsHelpers)
    {
        $this->commentsHelpers = $commentsHelpers;

        return $this;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        $settings = $this->config->getSettings('files');

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all(), $settings);
        }

        $units = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB'];

        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $options = [];
            $options[0]['name'] = 'comments';
            $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', '0', 'checked');
            $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
            $this->view->assign('options', $options);
        }

        $defaults = [
            'title' => '',
            'file_internal' => '',
            'file_external' => '',
            'filesize' => '',
            'text' => '',
            'start' => '',
            'end' => ''
        ];

        $this->formTokenHelper->generateFormToken();

        return [
            'units' => $this->get('core.helpers.forms')->selectGenerator('units', $units, $units, ''),
            'categories' => $this->categoriesHelpers->categoriesList('files', '', true),
            'checked_external' => $this->request->getPost()->has('external') ? ' checked="checked"' : '',
            'SEO_FORM_FIELDS' => $this->seo->formFields(),
            'form' => array_merge($defaults, $this->request->getPost()->all())
        ];
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

                $upload = new Core\Helpers\Upload('files');
                foreach ($items as $item) {
                    if (!empty($item)) {
                        $upload->removeUploadedFile($this->filesRepository->getFileById($item)); // Datei ebenfalls löschen
                        $bool = $this->filesRepository->delete($item);
                        if ($this->commentsHelpers) {
                            $this->commentsHelpers->deleteCommentsByModuleAndResult('files', $item);
                        }

                        $this->filesCache->getCacheDriver()->delete(Files\Cache::CACHE_ID);
                        $this->seo->deleteUriAlias(sprintf(Files\Helpers::URL_KEY_PATTERN, $item));
                    }
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
        $file = $this->filesRepository->getOneById($id);

        if (empty($file) === false) {
            $settings = $this->config->getSettings('files');

            $this->breadcrumb->setTitlePostfix($file['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $settings, $file, $id);
            }

            $units = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB'];

            $file['filesize'] = substr($file['size'], 0, strpos($file['size'], ' '));

            if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', $file['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'units' => $this->get('core.helpers.forms')->selectGenerator('units', $units, $units, trim(strrchr($file['size'], ' '))),
                'categories' => $this->categoriesHelpers->categoriesList('files', $file['category_id'], true),
                'checked_external' => $this->request->getPost()->has('external') ? ' checked="checked"' : '',
                'current_file' => $file['file'],
                'SEO_FORM_FIELDS' => $this->seo->formFields(sprintf(Files\Helpers::URL_KEY_PATTERN, $id)),
                'form' => array_merge($file, $this->request->getPost()->all())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $files = $this->filesRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($files)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/files/index/delete')
            ->setResourcePathEdit('admin/files/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->lang->t('system', 'publication_period'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::NAME,
                'fields' => ['start', 'end'],
                'default_sort' => true
            ], 50)
            ->addColumn([
                'label' => $this->lang->t('files', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['title'],
            ], 40)
            ->addColumn([
                'label' => $this->lang->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['description'],
            ], 30)
            ->addColumn([
                'label' => $this->lang->t('files', 'filesize'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['size'],
                'customer' => [
                    'default_value' => $this->lang->t('files', 'unknown_filesize')
                ]
            ], 20)
            ->addColumn([
                'label' => $this->lang->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::NAME,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($files) > 0
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

        $settings = $this->config->getSettings('files');

        if ($this->commentsHelpers) {
            $this->view->assign('comments', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('comments', $settings['comments']));
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->get('core.helpers.forms')->recordsPerPage((int)$settings['sidebar'], 1, 10)
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData, array $settings)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData, $settings) {
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } else {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->filesValidator->validate($formData, $file);

            if (is_array($file) === true) {
                $upload = new Core\Helpers\Upload('files');
                $result = $upload->moveFile($file['tmp_name'], $file['name']);
                $newFile = $result['name'];
                $filesize = $result['size'];
            } else {
                $formData['filesize'] = (float)$formData['filesize'];
                $newFile = $file;
                $filesize = $formData['filesize'] . ' ' . $formData['unit'];
            }

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'category_id' => $this->fetchCategoryId($formData),
                'file' => $newFile,
                'size' => $filesize,
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'comments' => $this->useComments($formData, $settings),
                'user_id' => $this->user->getUserId(),
            ];


            $lastId = $this->filesRepository->insert($insertValues);

            $this->seo->insertUriAlias(
                sprintf(Files\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $dl
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, array $settings, array $dl, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $dl, $id) {
            $file = [];
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->filesValidator->validate($formData, $file, sprintf(Helpers::URL_KEY_PATTERN, $id));

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'category_id' => $this->fetchCategoryId($formData),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'comments' => $this->useComments($formData, $settings),
                'user_id' => $this->user->getUserId(),
            ];

            // Falls eine neue Datei angegeben wurde, Änderungen durchführen
            if (!empty($file)) {
                $upload = new Core\Helpers\Upload('files');

                if (is_array($file) === true) {
                    $result = $upload->moveFile($file['tmp_name'], $file['name']);
                    $newFile = $result['name'];
                    $filesize = $result['size'];
                } else {
                    $formData['filesize'] = (float)$formData['filesize'];
                    $newFile = $file;
                    $filesize = $formData['filesize'] . ' ' . $formData['unit'];
                }
                // SQL Query für die Änderungen
                $newFileSql = [
                    'file' => $newFile,
                    'size' => $filesize,
                ];

                $upload->removeUploadedFile($dl['file']);

                $updateValues = array_merge($updateValues, $newFileSql);
            }

            $bool = $this->filesRepository->update($updateValues, $id);

            $this->seo->insertUriAlias(
                sprintf(Files\Helpers::URL_KEY_PATTERN, $id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->filesCache->saveCache($id);

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
            $this->filesValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar']
            ];

            if ($this->commentsHelpers) {
                $data['comments'] = $formData['comments'];
            }

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'files');
        });
    }

    /**
     * @param array $formData
     *
     * @return int
     */
    protected function fetchCategoryId(array $formData)
    {
        return !empty($formData['cat_create']) ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], 'files') : $formData['cat'];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useComments(array $formData, array $settings)
    {
        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }
}
