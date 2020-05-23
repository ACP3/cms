<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Categories\Helpers;
use ACP3\Modules\ACP3\Files\Helpers as FilesHelpers;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;

class AdminFileEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    private $categoriesHelpers;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        Helpers $categoriesHelpers,
        RequestInterface $request,
        Title $title,
        Translator $translator
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->request = $request;
        $this->title = $title;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(array $file): array
    {
        $this->title->setPageTitlePrefix($file['title']);

        $file['filesize'] = '';
        $file['file_external'] = '';

        $external = [
            1 => $this->translator->t('files', 'external_resource'),
        ];

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator('active', $file['active']),
            'units' => $this->formsHelper->choicesGenerator(
                'units',
                $this->getUnits(),
                \trim(\strrchr($file['size'], ' '))
            ),
            'categories' => $this->categoriesHelpers->categoriesList(
                FilesSchema::MODULE_NAME,
                $file['category_id'],
                true
            ),
            'external' => $this->formsHelper->checkboxGenerator('external', $external),
            'current_file' => $file['file'],
            'form' => \array_merge($file, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'SEO_URI_PATTERN' => FilesHelpers::URL_KEY_PATTERN,
            'SEO_ROUTE_NAME' => !empty($file['id']) ? \sprintf(FilesHelpers::URL_KEY_PATTERN, $file['id']) : '',
        ];
    }

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
}
