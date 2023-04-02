<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Wysiwygckeditor\WYSIWYG\Editor;

use ACP3\Core\ACL;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Assets\IncludeJs;
use ACP3\Core\I18n\Translator;
use ACP3\Core\WYSIWYG\Editor\Textarea;
use ACP3\Modules\ACP3\Filemanager\Helpers;

/**
 * Implementation of the AbstractWYSIWYG class for CKEditor.
 */
class CKEditor extends Textarea
{
    private bool $isInitialized = false;

    public function __construct(private readonly ACL $acl, private readonly FileResolver $fileResolver, private readonly IncludeJs $includeJs, private readonly Translator $translator, private readonly ?Helpers $filemanagerHelpers = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFriendlyName(): string
    {
        return 'CKEditor';
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $params = []): void
    {
        parent::setParameters($params);

        $this->config['toolbar'] = (isset($params['toolbar']) && $params['toolbar'] === 'simple') ? 'Basic' : 'Full';
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
            $wysiwyg['advanced_replace_content'] = 'CKEDITOR.instances.' . $wysiwyg['id'] . '.insertHtml(text);';
        }

        return ['wysiwyg' => $wysiwyg];
    }

    /**
     * Configures the CKEditor instance.
     */
    private function configure(): string
    {
        $this->config['entities'] = false;
        $this->config['extraPlugins'] = 'divarea,embed,codemirror';
        $this->config['allowedContent'] = true;
        $this->config['embed_provider'] = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}';
        $this->config['language'] = $this->translator->getShortIsoCode();
        $this->config['format_tags'] = 'h1;h2;h3;h4;h5;h6;pre;p;div;address';
        $this->config['codemirror'] = [
            'theme' => 'default',
            'lineNumbers' => true,
            'lineWrapping' => true,
            'matchBrackets' => true,
            'autoCloseTags' => true,
            'autoCloseBrackets' => true,
            'enableSearchTools' => true,
            'enableCodeFolding' => true,
            'enableCodeFormatting' => true,
            'autoFormatOnStart' => true,
            'autoFormatOnUncomment' => true,
            'highlightActiveLine' => true,
            'highlightMatches' => true,
            'showFormatButton' => false,
            'showCommentButton' => false,
            'showUncommentButton' => false,
        ];

        // Full toolbar
        if (!isset($this->config['toolbar']) || $this->config['toolbar'] !== 'Basic') {
            $this->configureFullToolbar();
        } else { // basic toolbar
            $this->configureBasicToolbar();
        }

        return json_encode($this->config, JSON_THROW_ON_ERROR);
    }

    /**
     * Prints javascript code.
     */
    private function script(string $js): string
    {
        $out = '<script>';
        $out .= $js;

        return $out . "</script>\n";
    }

    private function init(): string
    {
        if ($this->isInitialized === true) {
            return '';
        }

        $this->isInitialized = true;

        $ckeditorEntrypoint = $this->fileResolver->getWebStaticAssetPath('Wysiwygckeditor', 'Assets/js/ckeditor', 'ckeditor.js');
        $basePath = substr($ckeditorEntrypoint, 0, strrpos($ckeditorEntrypoint, '/') + 1);

        $out = '';

        // Skip relative paths...
        if (!str_starts_with($basePath, '..')) {
            $out .= $this->script("window.CKEDITOR_BASEPATH='" . $basePath . "';");
        }

        $out .= '<script src="' . $ckeditorEntrypoint . "\"></script>\n";

        $ckeditorPluginsDir = $basePath . 'plugins/';

        $js = "CKEDITOR.plugins.addExternal('codemirror', '" . $ckeditorPluginsDir . "codemirror/');\n";
        $js .= "CKEDITOR.plugins.addExternal('divarea', '" . $ckeditorPluginsDir . "divarea/');\n";
        $js .= "CKEDITOR.plugins.addExternal('embedbase', '" . $ckeditorPluginsDir . "embedbase/');\n";
        $js .= "CKEDITOR.plugins.addExternal('embed', '" . $ckeditorPluginsDir . "embed/');\n";
        $js .= 'CKEDITOR.dtd.$removeEmpty[\'i\'] = false;' . "\n";

        $out .= $this->script($js);

        return $out . $this->includeJs->add('Wysiwygckeditor', 'partials/ckeditor');
    }

    private function addFileManager(): void
    {
        if ($this->filemanagerHelpers === null) {
            return;
        }
        if (!$this->acl->hasPermission('admin/filemanager/index/richfilemanager')) {
            return;
        }

        $this->config['filebrowserBrowseUrl'] = $this->filemanagerHelpers->getFilemanagerPath();
    }

    private function configureFullToolbar(): void
    {
        $this->config['extraPlugins'] = 'codemirror,divarea,embedbase,embed';

        $this->addFileManager();

        // Toolbar configuration
        $this->config['toolbarGroups'] = [
            ['name' => 'document', 'groups' => ['mode', 'document', 'doctools']],
            ['name' => 'clipboard', 'groups' => ['clipboard', 'undo']],
            ['name' => 'editing', 'groups' => ['find', 'selection', 'spellchecker']],
            ['name' => 'forms'],
            '/',
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'paragraph', 'groups' => ['list', 'indent', 'blocks', 'align', 'bidi']],
            ['name' => 'links'],
            ['name' => 'insert'],
            '/',
            ['name' => 'styles'],
            ['name' => 'colors'],
            ['name' => 'tools'],
            ['name' => 'others'],
            ['name' => 'about'],
        ];
    }

    private function configureBasicToolbar(): void
    {
        $this->config['extraPlugins'] = 'divarea,codemirror';
        $this->config['toolbar'] = [
            [
                'Source',
                '-',
                'Undo',
                'Redo',
                '-',
                'Bold',
                'Italic',
                '-',
                'NumberedList',
                'BulletedList',
                '-',
                'Link',
                'Unlink',
                '-',
                'About',
            ],
        ];
    }
}
