<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AdminController;
use ACP3\Modules\ACP3\Categories;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Modules\ACP3\Categories\Helpers      $categoriesHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Categories\Helpers $categoriesHelpers)
    {
        parent::__construct($context);

        $this->categoriesHelpers = $categoriesHelpers;
    }


    /**
     * @param array $formData
     *
     * @return int
     */
    protected function fetchCategoryIdForSave(array $formData)
    {
        return !empty($formData['cat_create']) ? $this->categoriesHelpers->categoriesCreate($formData['cat_create'], 'news') : $formData['cat'];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useReadMore(array $formData, array $settings)
    {
        return $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0;
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useComments(array $formData, array $settings)
    {
        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }

    /**
     * @param array $settings
     * @param int   $readMoreValue
     * @param int   $commentsValue
     *
     * @return array
     */
    protected function fetchNewsOptions(array $settings, $readMoreValue, $commentsValue)
    {
        $options = [];
        if ($settings['readmore'] == 1) {
            $options[] = [
                'name' => 'readmore',
                'checked' => $this->get('core.helpers.forms')->selectEntry('readmore', '1', $readMoreValue, 'checked'),
                'lang' => $this->translator->t('news', 'activate_readmore')
            ];
        }
        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $options[] = [
                'name' => 'comments',
                'checked' => $this->get('core.helpers.forms')->selectEntry('comments', '1', $commentsValue, 'checked'),
                'lang' => $this->translator->t('system', 'allow_comments')
            ];
        }

        return $options;
    }
}