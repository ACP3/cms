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
    private bool $initialized = false;

    public function __construct(private readonly Core\ACL $acl, private readonly Core\View $view, private readonly ?Helpers $filemanagerHelpers = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFriendlyName(): string
    {
        return 'TinyMCE';
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $params = []): void
    {
        parent::setParameters($params);

        $this->config['toolbar'] = $params['toolbar'] ?? '';
        $this->config['height'] = ($params['height'] ?? 250) . 'px';
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        $wysiwyg = [
            'friendly_name' => $this->getFriendlyName(),
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'js' => $this->init(),
            'advanced' => $this->advanced,
            'required' => $this->required,
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

    private function configure(): string
    {
        $config = [
            'selector' => 'textarea#' . $this->id,
            'theme' => 'modern',
            'height' => $this->config['height'],
        ];

        $this->configurePlugins();
        $this->configureToolbar();
        $this->configureAdvancedImages();

        return json_encode(array_merge(
            $config,
            $this->configurePlugins(),
            $this->configureToolbar(),
            $this->configureAdvancedImages()
        ), JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, string[]>
     */
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

    /**
     * @return array<string, string>
     */
    private function configureToolbar(): array
    {
        if ($this->isSimpleEditor()) {
            $toolbar = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image';
        } else {
            $toolbar = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons';
        }

        return ['toolbar' => $toolbar];
    }

    /**
     * @return array<string, bool>
     */
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
