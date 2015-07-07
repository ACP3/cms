<?php

namespace ACP3\Modules\ACP3\Files\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Files;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Files\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model
     */
    protected $filesModel;
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
     * @param \ACP3\Core\Context\Admin         $context
     * @param \ACP3\Core\Date                  $date
     * @param \ACP3\Core\Helpers\Secure        $secureHelper
     * @param \ACP3\Modules\ACP3\Files\Model        $filesModel
     * @param \ACP3\Modules\ACP3\Files\Cache        $filesCache
     * @param \ACP3\Modules\ACP3\Files\Validator    $filesValidator
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Files\Model $filesModel,
        Files\Cache $filesCache,
        Files\Validator $filesValidator,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->filesModel = $filesModel;
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

    public function actionCreate()
    {
        $settings = $this->config->getSettings('files');

        if (empty($_POST) === false) {
            $this->_createPost($_POST, $settings);
        }

        // Datumsauswahl
        $this->view->assign('publication_period', $this->date->datepicker(['start', 'end']));

        $units = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB'];
        $this->view->assign('units', $this->get('core.helpers.forms')->selectGenerator('units', $units, $units, ''));

        // Formularelemente
        $this->view->assign('categories', $this->categoriesHelpers->categoriesList('files', '', true));

        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $options = [];
            $options[0]['name'] = 'comments';
            $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', '0', 'checked');
            $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
            $this->view->assign('options', $options);
        }

        $this->view->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');

        $defaults = [
            'title' => '',
            'file_internal' => '',
            'file_external' => '',
            'filesize' => '',
            'text' => '',
        ];

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
            $bool = false;

            $upload = new Core\Helpers\Upload('files');
            foreach ($items as $item) {
                if (!empty($item)) {
                    $upload->removeUploadedFile($this->filesModel->getFileById($item)); // Datei ebenfalls löschen
                    $bool = $this->filesModel->delete($item);
                    if ($this->commentsHelpers) {
                        $this->commentsHelpers->deleteCommentsByModuleAndResult('files', $item);
                    }

                    $this->filesCache->getCacheDriver()->delete(Files\Cache::CACHE_ID);
                    $this->seo->deleteUriAlias(sprintf(Files\Helpers::URL_KEY_PATTERN, $item));
                }
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $file = $this->filesModel->getOneById((int)$this->request->id);

        if (empty($file) === false) {
            $settings = $this->config->getSettings('files');

            $this->breadcrumb->setTitlePostfix($file['title']);

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings, $file);
            }

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(['start', 'end'], [$file['start'], $file['end']]));

            $units = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB'];
            $this->view->assign('units', $this->get('core.helpers.forms')->selectGenerator('units', $units, $units, trim(strrchr($file['size'], ' '))));

            $file['filesize'] = substr($file['size'], 0, strpos($file['size'], ' '));

            // Formularelemente
            $this->view->assign('categories', $this->categoriesHelpers->categoriesList('files', $file['category_id'], true));

            if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', $file['comments'], 'checked');
                $options[0]['lang'] = $this->lang->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            $this->view->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');
            $this->view->assign('current_file', $file['file']);

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(Files\Helpers::URL_KEY_PATTERN, $this->request->id)));
            $this->view->assign('form', array_merge($file, $this->request->getPost()->getAll()));

            $this->secureHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $files = $this->filesModel->getAllInAcp();

        if (count($files) > 0) {
            $canDelete = $this->acl->hasPermission('admin/files/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('files', $files);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
        }

        $settings = $this->config->getSettings('files');

        if ($this->commentsHelpers) {
            $lang_comments = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('comments', $this->get('core.helpers.forms')->selectGenerator('comments', [1, 0], $lang_comments, $settings['comments'], 'checked'));
        }

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        $this->view->assign('sidebar_entries', $this->get('core.helpers.forms')->recordsPerPage((int)$settings['sidebar'], 1, 10));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    protected function _createPost(array $formData, array $settings)
    {
        try {
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } else {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->filesValidator->validateCreate($formData, $file);

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
                'category_id' => !empty($formData['cat_create']) ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], 'files') : $formData['cat'],
                'file' => $newFile,
                'size' => $filesize,
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'comments' => $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0,
                'user_id' => $this->auth->getUserId(),
            ];


            $lastId = $this->filesModel->insert($insertValues);

            $this->seo->insertUriAlias(
                sprintf(Files\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $dl
     */
    protected function _editPost(array $formData, array $settings, array $dl)
    {
        try {
            $file = [];
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->filesValidator->validateEdit($formData, $file);

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'category_id' => !empty($formData['cat_create']) ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], 'files') : $formData['cat'],
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'comments' => $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0,
                'user_id' => $this->auth->getUserId(),
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

            $bool = $this->filesModel->update($updateValues, $this->request->id);

            $this->seo->insertUriAlias(
                sprintf(Files\Helpers::URL_KEY_PATTERN, $this->request->id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->filesCache->setCache($this->request->id);

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        try {
            $this->filesValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar']
            ];

            if ($this->commentsHelpers) {
                $data['comments'] = $formData['comments'];
            }

            $bool = $this->config->setSettings($data, 'files');

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
