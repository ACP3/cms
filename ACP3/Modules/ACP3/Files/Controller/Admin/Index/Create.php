<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    protected $commentsHelpers;
    /**
     * @var Files\Model\FilesModel
     */
    protected $filesModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Files\Model\FilesModel $filesModel
     * @param \ACP3\Modules\ACP3\Files\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Files\Model\FilesModel $filesModel,
        Files\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $categoriesHelpers);

        $this->adminFormValidation = $adminFormValidation;
        $this->filesModel = $filesModel;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } else {
                $file = $this->request->getFiles()->get('file_internal');
            }

            $this->adminFormValidation
                ->setFile($file)
                ->validate($formData);

            if (is_array($file) === true) {
                $upload = new Core\Helpers\Upload($this->appPath, Files\Installer\Schema::MODULE_NAME);
                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['file'] = $result['name'];
                $formData['filesize'] = $result['size'];
            } else {
                $formData['file'] = $file;
                $formData['filesize'] = ((float)$formData['filesize']) . ' ' . $formData['unit'];
            }

            $formData['cat'] = $this->fetchCategoryId($formData);
            $formData['comments'] = $this->useComments($formData);
            $formData['user_id'] = $this->user->getUserId();

            return $this->filesModel->save($formData);
        });
    }
}
