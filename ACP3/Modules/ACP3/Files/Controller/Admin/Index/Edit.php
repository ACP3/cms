<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
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
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext              $context
     * @param \ACP3\Core\Date                                         $date
     * @param \ACP3\Core\Helpers\Forms                                $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository          $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache                          $filesCache
     * @param \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers                   $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Cache $filesCache,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $categoriesHelpers);

        $this->date = $date;
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $file = $this->filesRepository->getOneById($id);

        if (empty($file) === false) {
            $settings = $this->config->getSettings('files');

            $this->title->setPageTitlePostfix($file['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $settings, $file, $id);
            }

            $file['filesize'] = substr($file['size'], 0, strpos($file['size'], ' '));

            if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
                $options = [];
                $options[0]['name'] = 'comments';
                $options[0]['checked'] = $this->formsHelper->selectEntry('comments', '1', $file['comments'], 'checked');
                $options[0]['lang'] = $this->translator->t('system', 'allow_comments');
                $this->view->assign('options', $options);
            }

            return [
                'units' => $this->formsHelper->choicesGenerator('units', $this->getUnits(),
                    trim(strrchr($file['size'], ' '))),
                'categories' => $this->categoriesHelpers->categoriesList('files', $file['category_id'], true),
                'checked_external' => $this->request->getPost()->has('external') ? ' checked="checked"' : '',
                'current_file' => $file['file'],
                'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper
                    ? $this->metaFormFieldsHelper->formFields(sprintf(Files\Helpers::URL_KEY_PATTERN, $id))
                    : [],
                'form' => array_merge($file, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param array $dl
     * @param int   $fileId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, array $dl, $fileId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $dl, $fileId) {
            $file = [];
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } elseif ($this->request->getFiles()->has('file_internal')) {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->adminFormValidation
                ->setFile($file)
                ->setUriAlias(sprintf(Helpers::URL_KEY_PATTERN, $fileId))
                ->validate($formData);

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'category_id' => $this->fetchCategoryId($formData),
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                'text' => $this->get('core.helpers.secure')->strEncode($formData['text'], true),
                'comments' => $this->useComments($formData, $settings),
                'user_id' => $this->user->getUserId(),
            ];

            if (!empty($file)) {
                $newFileSql = $this->updateAssociatedFile($file, $formData, $dl['file']);

                $updateValues = array_merge($updateValues, $newFileSql);
            }

            $bool = $this->filesRepository->update($updateValues, $fileId);

            $this->insertUriAlias($formData, $fileId);

            $this->filesCache->saveCache($fileId);

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        });
    }

    /**
     * @param string|array $file
     * @param array        $formData
     * @param string       $currentFileName
     *
     * @return array
     */
    protected function updateAssociatedFile($file, array $formData, $currentFileName)
    {
        $upload = new Core\Helpers\Upload($this->appPath, 'files');

        if (is_array($file) === true) {
            $result = $upload->moveFile($file['tmp_name'], $file['name']);
            $newFile = $result['name'];
            $fileSize = $result['size'];
        } else {
            $formData['filesize'] = (float)$formData['filesize'];
            $newFile = $file;
            $fileSize = $formData['filesize'] . ' ' . $formData['unit'];
        }

        $upload->removeUploadedFile($currentFileName);

        return [
            'file' => $newFile,
            'size' => $fileSize,
        ];
    }
}
