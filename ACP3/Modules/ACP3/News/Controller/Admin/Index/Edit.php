<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    use CommentsHelperTrait;

    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\News\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var News\Model\NewsModel
     */
    protected $newsModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\News\Model\Repository\NewsRepository $newsRepository
     * @param News\Model\NewsModel $newsModel
     * @param \ACP3\Modules\ACP3\News\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        News\Model\Repository\NewsRepository $newsRepository,
        News\Model\NewsModel $newsModel,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->formTokenHelper = $formTokenHelper;
        $this->newsRepository = $newsRepository;
        $this->newsModel = $newsModel;
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
        $news = $this->newsRepository->getOneById($id);

        if (empty($news) === false) {
            $this->title->setPageTitlePostfix($news['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            return [
                'categories' => $this->categoriesHelpers->categoriesList(
                    News\Installer\Schema::MODULE_NAME,
                    $news['category_id'],
                    true
                ),
                'options' => $this->fetchOptions($news['readmore'], $news['comments']),
                'target' => $this->formsHelper->linkTargetChoicesGenerator('target', $news['target']),
                'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper
                    ? $this->metaFormFieldsHelper->formFields(sprintf(News\Helpers::URL_KEY_PATTERN, $id))
                    : [],
                'form' => array_merge($news, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param int $newsId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $newsId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $newsId) {
            $this->adminFormValidation
                ->setUriAlias(sprintf(News\Helpers::URL_KEY_PATTERN, $newsId))
                ->validate($formData);

            $formData['cat'] = $this->fetchCategoryIdForSave($formData);
            $bool = $this->newsModel->saveNews($formData, $this->user->getUserId(), $newsId);

            $this->insertUriAlias($formData, $newsId);

            return $bool;
        });
    }
}
