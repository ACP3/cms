<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\Files\Helpers;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Files\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
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
     * @var \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    protected $commentsHelpers;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext              $context
     * @param \ACP3\Core\Date                                         $date
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Modules\ACP3\Files\Model\FilesRepository          $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache                          $filesCache
     * @param \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers                   $categoriesHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Model\FilesRepository $filesRepository,
        Files\Cache $filesCache,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context, $categoriesHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $file = $this->filesRepository->getOneById($id);

        if (empty($file) === false) {
            $settings = $this->config->getSettings('files');

            $this->breadcrumb->setTitlePostfix($file['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->editPost($this->request->getPost()->all(), $settings, $file, $id);
            }

            $units = ['Byte', 'KiB', 'MiB', 'GiB', 'TiB'];

            $file['filesize'] = substr($file['size'], 0, strpos($file['size'], ' '));

            if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('comments', '1', $file['comments'], 'checked');
                $options[0]['lang'] = $this->translator->t('system', 'allow_comments');
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
     * @param array $formData
     * @param array $settings
     * @param array $dl
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function editPost(array $formData, array $settings, array $dl, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $dl, $id) {
            $file = [];
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->adminFormValidation
                ->setFile($file)
                ->setUriAlias(sprintf(Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

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
                $upload = new Core\Helpers\Upload($this->appPath, 'files');

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
}
