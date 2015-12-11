<?php

namespace ACP3\Modules\ACP3\Seo\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\SeoRepository
     */
    protected $seoRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\FormValidation
     */
    protected $seoValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext       $context
     * @param \ACP3\Core\Helpers\FormToken                     $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Cache                     $seoCache
     * @param \ACP3\Modules\ACP3\Seo\Model\SeoRepository       $seoRepository
     * @param \ACP3\Modules\ACP3\Seo\Validation\FormValidation $seoValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Cache $seoCache,
        Seo\Model\SeoRepository $seoRepository,
        Seo\Validation\FormValidation $seoValidator)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->seoCache = $seoCache;
        $this->seoRepository = $seoRepository;
        $this->seoValidator = $seoValidator;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all());
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'SEO_FORM_FIELDS' => $this->seo->formFields(),
            'form' => array_merge(['uri' => ''], $this->request->getPost()->all())
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

                foreach ($items as $item) {
                    $bool = $this->seoRepository->delete($item);
                }

                $this->seoCache->saveCache();

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
        $seo = $this->seoRepository->getOneById($id);

        if (empty($seo) === false) {
            $this->breadcrumb->setTitlePostfix($seo['alias']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $seo['uri'], $id);
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'SEO_FORM_FIELDS' => $this->seo->formFields($seo['uri']),
                'form' => array_merge(['uri' => $seo['uri']], $this->request->getPost()->all())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $seo = $this->seoRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($seo)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/seo/index/delete')
            ->setResourcePathEdit('admin/seo/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('seo', 'uri'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['uri'],
                'default_sort' => true
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('seo', 'alias'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['alias'],
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('seo', 'keywords'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['keywords'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('seo', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['description'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('seo', 'robots'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::NAME,
                'fields' => ['robots'],
                'custom' => [
                    'search' => [0, 1, 2, 3, 4],
                    'replace' => [
                        $this->translator->t('seo', 'robots_use_system_default',
                            ['%default%' => $this->seo->getRobotsSetting()]),
                        $this->translator->t('seo', 'robots_index_follow'),
                        $this->translator->t('seo', 'robots_index_nofollow'),
                        $this->translator->t('seo', 'robots_noindex_follow'),
                        $this->translator->t('seo', 'robots_noindex_nofollow')
                    ]
                ]
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::NAME,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($seo) > 0
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->seoValidator->validate($formData);

            $bool = $this->seo->insertUriAlias(
                $formData['uri'],
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @param array  $formData
     * @param string $path
     * @param int    $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, $path, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $path, $id) {
            $this->seoValidator->validate($formData, $path);

            $updateValues = [
                'uri' => $formData['uri'],
                'alias' => $formData['alias'],
                'keywords' => Core\Functions::strEncode($formData['seo_keywords']),
                'description' => Core\Functions::strEncode($formData['seo_description']),
                'robots' => (int)$formData['seo_robots']
            ];

            $bool = $this->seoRepository->update($updateValues, $id);

            $this->seoCache->saveCache();

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all());
        }

        $seoSettings = $this->config->getSettings('seo');

        // Robots
        $lang_robots = [
            $this->translator->t('seo', 'robots_index_follow'),
            $this->translator->t('seo', 'robots_index_nofollow'),
            $this->translator->t('seo', 'robots_noindex_follow'),
            $this->translator->t('seo', 'robots_noindex_nofollow')
        ];

        $this->formTokenHelper->generateFormToken();

        return [
            'robots' => $this->get('core.helpers.forms')->selectGenerator('robots', [1, 2, 3, 4], $lang_robots, $seoSettings['robots']),
            'mod_rewrite' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('mod_rewrite', $seoSettings['mod_rewrite']),
            'form' => array_merge($seoSettings, $this->request->getPost()->all())
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->seoValidator->validateSettings($formData);

            // Config aktualisieren
            $data = [
                'meta_description' => Core\Functions::strEncode($formData['meta_description']),
                'meta_keywords' => Core\Functions::strEncode($formData['meta_keywords']),
                'mod_rewrite' => (int)$formData['mod_rewrite'],
                'robots' => (int)$formData['robots'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $bool = $this->config->setSettings($data, 'seo');

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}