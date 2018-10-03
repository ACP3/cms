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
     * @var bool
     */
    private $initialized = false;

    /**
     * @param \ACP3\Core\Assets\Minifier\MinifierInterface $minifier
     * @param \ACP3\Core\I18n\Translator                   $translator
     * @param \ACP3\Core\Environment\ApplicationPath       $appPath
     * @param \ACP3\Modules\ACP3\Filemanager\Helpers|null  $filemanagerHelpers
     */
    public function __construct(
        Core\Assets\Minifier\MinifierInterface $minifier,
        Core\I18n\Translator $translator,
        Core\Environment\ApplicationPath $appPath,
        ?Helpers $filemanagerHelpers = null
    ) {
        $this->minifier = $minifier;
        $this->translator = $translator;
        $this->appPath = $appPath;
        $this->filemanagerHelpers = $filemanagerHelpers;
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

        $this->config['toolbar'] = (isset($params['toolbar'])) ? $params['toolbar'] : '';
        $this->config['height'] = ((isset($params['height'])) ? $params['height'] : 250) . 'px';
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
            'js' => $this->configure(),
            'advanced' => $this->advanced,
        ];

        if ($wysiwyg['advanced'] === true) {
            $wysiwyg['advanced_replace_content'] = 'tinyMCE.execInstanceCommand(\'' . $this->id . '\',"mceInsertContent",false,text);';
        }

        return ['wysiwyg' => $wysiwyg];
    }

    /**
     * @return array
     */
    private function configure()
    {
        $config = [
            'is_initialized' => $this->initialized,
            'selector' => 'textarea#' . $this->id,
            'theme' => 'modern',
            'height' => $this->config['height'],
            'content_css' => $this->minifier->getURI(),
        ];

        if ($this->initialized === false) {
            $this->initialized = true;
        }

        // Basic editor
        if (isset($this->config['toolbar']) && $this->config['toolbar'] === 'simple') {
            $plugins = [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste',
            ];
            $toolbar = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image';
            $imagesAdvanced = 'false';
        } else { // Full editor
            $plugins = [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker',
            ];
            $toolbar = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons';
            $imagesAdvanced = 'true';

            if ($this->filemanagerHelpers instanceof \ACP3\Modules\ACP3\Filemanager\Helpers) {
                $config['filemanager_path'] = $this->filemanagerHelpers->getFilemanagerPath();
            }
        }

        $config['plugins'] = \json_encode($plugins);
        $config['toolbar'] = $toolbar;
        $config['image_advtab'] = $imagesAdvanced;

        return [
            'template' => 'Wysiwygtinymce/tinymce.tpl',
            'config' => $config,
        ];
    }
}
