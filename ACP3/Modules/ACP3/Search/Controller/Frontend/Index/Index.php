<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Search\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Search;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Search\Helpers
     */
    protected $searchHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Search\Validation\FormValidation
     */
    protected $searchValidator;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $formBlock;
    /**
     * @var Core\Helpers\Secure
     */
    private $secure;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $formBlock
     * @param Core\View\Block\BlockInterface $block
     * @param Core\Helpers\Secure $secure
     * @param \ACP3\Modules\ACP3\Search\Helpers $searchHelpers
     * @param \ACP3\Modules\ACP3\Search\Validation\FormValidation $searchValidator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $formBlock,
        Core\View\Block\BlockInterface $block,
        Core\Helpers\Secure $secure,
        Search\Helpers $searchHelpers,
        Search\Validation\FormValidation $searchValidator
    ) {
        parent::__construct($context);

        $this->searchHelpers = $searchHelpers;
        $this->searchValidator = $searchValidator;
        $this->formBlock = $formBlock;
        $this->secure = $secure;
        $this->block = $block;
    }

    /**
     * @param string $q
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($q = '')
    {
        if (!empty($q)) {
            return $this->executePost(['search_term' => (string)$q]);
        }

        return $this->formBlock
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param array $formData
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function executePost(array $formData = [])
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $formData = $this->prepareFormData($formData);

                $this->searchValidator->validate($formData);

                return $this->renderSearchResults(
                    $formData['mods'],
                    $this->secure->strEncode($formData['search_term']),
                    $formData['area'],
                    strtoupper($formData['sort'])
                );
            }
        );
    }

    /**
     * @param array $formData
     * @return array
     */
    protected function prepareFormData(array $formData)
    {
        if (isset($formData['search_term']) === true) {
            if (isset($formData['mods']) === false) {
                $modules = $this->searchHelpers->getModules();

                $formData['mods'] = [];
                foreach ($modules as $row) {
                    $formData['mods'][] = $row['dir'];
                }
            }
            if (isset($formData['area']) === false) {
                $formData['area'] = 'title_content';
            }
            if (isset($formData['sort']) === false) {
                $formData['sort'] = 'asc';
            }
        } else {
            $formData = $this->request->getPost()->all();
        }

        return $formData;
    }

    /**
     * @param array $modules
     * @param string $searchTerm
     * @param string $area
     * @param string $sort
     * @return array
     */
    protected function renderSearchResults(array $modules, string $searchTerm, string $area, string $sort)
    {
        return $this->block
            ->setData([
                'modules' => $modules,
                'search_term' => $searchTerm,
                'area' => $area,
                'sort' => $sort
            ])
            ->render();
    }
}
