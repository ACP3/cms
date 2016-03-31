<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\News;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    use CommentsHelperTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\News\Cache
     */
    protected $newsCache;
    /**
     * @var \ACP3\Modules\ACP3\News\Validation\AdminFormValidation
     */
    protected $adminFormValidation;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext             $context
     * @param \ACP3\Core\Date                                        $date
     * @param \ACP3\Core\Helpers\Forms                               $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                           $formTokenHelper
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository           $newsRepository
     * @param \ACP3\Modules\ACP3\News\Cache                          $newsCache
     * @param \ACP3\Modules\ACP3\News\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers                  $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        News\Model\NewsRepository $newsRepository,
        News\Cache $newsCache,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsRepository = $newsRepository;
        $this->newsCache = $newsCache;
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
        $news = $this->newsRepository->getOneById($id);

        if (empty($news) === false) {
            $this->breadcrumb->setTitlePostfix($news['title']);

            $settings = $this->config->getSettings('news');

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $settings, $id);
            }

            return [
                'categories' => $this->categoriesHelpers->categoriesList('news', $news['category_id'], true),
                'options' => $this->fetchNewsOptions($settings, $news['readmore'], $news['comments']),
                'target' => $this->formsHelper->linkTargetChoicesGenerator('target', $news['target']),
                'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper
                    ? $this->metaFormFieldsHelper->formFields(sprintf(News\Helpers::URL_KEY_PATTERN, $id))
                    : [],
                'form' => array_merge($news, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $settings, $id) {
            $this->adminFormValidation
                ->setUriAlias(sprintf(News\Helpers::URL_KEY_PATTERN, $id))
                ->validate($formData);

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                'text' => $this->get('core.helpers.secure')->strEncode($formData['text'], true),
                'readmore' => $this->useReadMore($formData, $settings),
                'comments' => $this->useComments($formData, $settings),
                'category_id' => $this->fetchCategoryIdForSave($formData),
                'uri' => $this->get('core.helpers.secure')->strEncode($formData['uri'], true),
                'target' => (int)$formData['target'],
                'link_title' => $this->get('core.helpers.secure')->strEncode($formData['link_title']),
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->newsRepository->update($updateValues, $id);

            $this->seo->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->newsCache->saveCache($id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
