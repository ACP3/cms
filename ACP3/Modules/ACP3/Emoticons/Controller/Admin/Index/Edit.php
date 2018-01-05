<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Emoticons\Model\EmoticonsModel
     */
    protected $emoticonsModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Emoticons\Model\EmoticonsModel $emoticonsModel
     * @param \ACP3\Modules\ACP3\Emoticons\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Emoticons\Model\EmoticonsModel $emoticonsModel,
        Emoticons\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->emoticonsModel = $emoticonsModel;
        $this->block = $block;
    }

    /**
     * @param int|null $id
     *
     * @return array
     */
    public function execute(?int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int|null $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();
            $file = $this->request->getFiles()->get('picture');

            if ($id === null) {
                $this->adminFormValidation->setFileRequired(true);
            }

            $this->adminFormValidation
                ->setFile($file)
                ->validate($formData);

            if (empty($file) === false) {
                $upload = new Core\Helpers\Upload($this->appPath, Emoticons\Installer\Schema::MODULE_NAME);

                if ($id !== null) {
                    $emoticon = $this->emoticonsModel->getOneById($id);
                    $upload->removeUploadedFile($emoticon['img']);
                }

                $result = $upload->moveFile($file->getPathname(), $file->getClientOriginalName());
                $formData['img'] = $result['name'];
            }

            return $this->emoticonsModel->save($formData, $id);
        });
    }
}
