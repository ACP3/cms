<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\News;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
class Create extends AbstractFormAction
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
     * @var \ACP3\Modules\ACP3\News\Validation\AdminFormValidation
     */
    protected $adminFormValidation;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext             $context
     * @param \ACP3\Core\Date                                        $date
     * @param \ACP3\Core\Helpers\Forms                               $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                           $formTokenHelper
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository           $newsRepository
     * @param \ACP3\Modules\ACP3\News\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Categories\Helpers                  $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        News\Model\NewsRepository $newsRepository,
        News\Validation\AdminFormValidation $adminFormValidation,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context, $formsHelper, $categoriesHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsRepository = $newsRepository;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings('news');

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all(), $settings);
        }

        $defaults = [
            'title' => '',
            'text' => '',
            'uri' => '',
            'link_title' => '',
            'start' => '',
            'end' => ''
        ];

        return [
            'categories' => $this->categoriesHelpers->categoriesList('news', '', true),
            'options' => $this->fetchNewsOptions($settings, 0, 0),
            'target' => $this->formsHelper->linkTargetChoicesGenerator('target'),
            'SEO_FORM_FIELDS' => $this->seo->formFields(),
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData, $settings) {
            $this->adminFormValidation->validate($formData);

            $insertValues = [
                'id' => '',
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

            $lastId = $this->newsRepository->insert($insertValues);

            $this->seo->insertUriAlias(
                sprintf(News\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }
}
