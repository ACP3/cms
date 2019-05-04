<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Wysiwygtinymce\WYSIWYG\Editor;

use ACP3\Core;
use ACP3\Modules\ACP3\Filemanager\Helpers;

/**
 * Implementation of the AbstractWYSIWYG class for TinyMCE.
 */
class TinyMCE extends Core\WYSIWYG\Editor\Textarea
{
    /**
     * @var \ACP3\Core\Assets\Minifier\MinifierInterface
     */
    protected $minifier;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Modules\ACP3\Filemanager\Helpers|null
     */
    protected $filemanagerHelpers;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    /**
     * @var bool
     */
    private $initialized = false;

    public function __construct(
        Core\ACL $acl,
        Core\Assets\Minifier\MinifierInterface $minifier,
        Core\I18n\Translator $translator,
        Core\Environment\ApplicationPath $appPath,
        Core\View $view,
        ?Helpers $filemanagerHelpers = null
    ) {
        $this->minifier = $minifier;
        $this->translator = $translator;
        $this->appPath = $appPath;
        $this->filemanagerHelpers = $filemanagerHelpers;
        $this->acl = $acl;
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function getFriendlyName()
    {
        return 'TinyMCE';
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $params = [])
    {
        parent::setParameters($params);

        $this->config['toolbar'] = $params['toolbar'] ?? '';
        $this->config['height'] = ($params['height'] ?? 250) . 'px';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $wysiwyg = [
            'friendly_name' => $this->getFriendlyName(),
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'js' => $this->init(),
            'advanced' => $this->advanced,
            'data_config' => $this->configure(),
        ];

        if ($wysiwyg['advanced'] === true) {
            $wysiwyg['advanced_replace_content'] = 'tinyMCE.execInstanceCommand(\'' . $this->id . '\',"mceInsertContent",false,text);';
        }

        return ['wysiwyg' => $wysiwyg];
    }

    private function init(): string
    {
        if ($this->initialized) {
            return '';
        }

        $this->view->assign('tinymce', [
            'initialized' => $this->initialized,
            'filemanager_path' => $this->getFileManagerPath(),
        ]);

        $this->initialized = true;

        return $this->view->fetchTemplate('Wysiwygtinymce/tinymce.tpl');
    }

    private function getFileManagerPath(): ?string
    {
        if ($this->filemanagerHelpers === null) {
            return null;
        }
        if (!$this->acl->hasPermission('admin/filemanager/index/richfilemanager')) {
            return null;
        }
        if ($this->isSimpleEditor()) {
            return null;
        }

        return $this->filemanagerHelpers->getFilemanagerPath();
    }

    /**
     * @return string
     */
    private function configure(): string
    {
        $config = [
            'selector' => 'textarea#' . $this->id,
            'theme' => 'modern',
            'height' => $this->config['height'],
            'content_css' => $this->minifier->getURI(),
        ];

        $this->configurePlugins();
        $this->configureToolbar();
        $this->configureAdvancedImages();

        return \json_encode(\array_merge(
            $config,
            $this->configurePlugins(),
            $this->configureToolbar(),
            $this->configureAdvancedImages()
        ));
    }

    private function configurePlugins(): array
    {
        if ($this->isSimpleEditor()) {
            $plugins = [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste',
            ];
        } else {
            $plugins = [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker',
            ];
        }

        return ['plugins' => $plugins];
    }

    private function configureToolbar(): array
    {
        if ($this->isSimpleEditor()) {
            $toolbar = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image';
        } else {
            $toolbar = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons';
        }

        return ['toolbar' => $toolbar];
    }

    private function configureAdvancedImages(): array
    {
        return [
            'image_advtab' => !$this->isSimpleEditor(),
        ];
    }

    private function isSimpleEditor(): bool
    {
        return isset($this->config['toolbar']) && $this->config['toolbar'] === 'simple';
    }
}
