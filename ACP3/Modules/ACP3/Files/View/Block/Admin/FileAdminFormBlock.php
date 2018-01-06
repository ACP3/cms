<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Files\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class FileAdminFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    private $categoriesHelpers;

    /**
     * FileFormBlock constructor.
     * @param FormBlockContext $context
     * @param FilesRepository $filesRepository
     * @param SettingsInterface $settings
     * @param Modules $modules
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
     */
    public function __construct(
        FormBlockContext $context,
        FilesRepository $filesRepository,
        SettingsInterface $settings,
        Modules $modules,
        \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelpers
    ) {
        parent::__construct($context, $filesRepository);

        $this->settings = $settings;
        $this->modules = $modules;
        $this->categoriesHelpers = $categoriesHelpers;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->setTemplate(
            $this->getId() ? 'Files/Admin/index.manage_with_id.tpl' : 'Files/Admin/index.manage.tpl'
        );

        $this->breadcrumb->setLastStepReplacement(
            $this->translator->t('files', !$this->getId() ? 'admin_index_create' : 'admin_index_edit')
        );

        $this->title->setPageTitlePrefix($data['title']);

        $external = [
            1 => $this->translator->t('files', 'external_resource'),
        ];

        return [
            'active' => $this->forms->yesNoCheckboxGenerator('active', $data['active']),
            'options' => $this->getOptions(['comments' => '0']),
            'units' => $this->forms->choicesGenerator(
                'units',
                $this->getUnits(),
                \trim(\strrchr($data['size'], ' '))
            ),
            'categories' => $this->categoriesHelpers->categoriesList(
                Schema::MODULE_NAME,
                $data['category_id'],
                true
            ),
            'external' => $this->forms->checkboxGenerator('external', $external),
            'current_file' => $data['file'],
            'form' => \array_merge($data, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
            'SEO_URI_PATTERN' => Helpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => $this->getSeoRouteName($this->getId()),
        ];
    }

    /**
     * @param array $file
     * @return array
     */
    protected function getOptions(array $file): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $options = [];
        if ($settings['comments'] == 1 && $this->modules->isActive('comments') === true) {
            $comments = [
                '1' => $this->translator->t('system', 'allow_comments'),
            ];

            $options = $this->forms->checkboxGenerator('comments', $comments, $file['comments']);
        }

        return $options;
    }

    /**
     * @return array
     */
    private function getUnits(): array
    {
        return [
            'Byte' => 'Byte',
            'KiB' => 'KiB',
            'MiB' => 'MiB',
            'GiB' => 'GiB',
            'TiB' => 'TiB',
        ];
    }

    /**
     * @param int|null $id
     * @return string
     */
    private function getSeoRouteName(?int $id): string
    {
        return !empty($id) ? \sprintf(Helpers::URL_KEY_PATTERN, $id) : '';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'id' => '',
            'active' => 1,
            'title' => '',
            'category_id' => 0,
            'file_internal' => '',
            'file_external' => '',
            'size' => '',
            'file' => '',
            'filesize' => '',
            'text' => '',
            'start' => '',
            'end' => '',
        ];
    }
}
