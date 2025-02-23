<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Wysiwygckeditor\WYSIWYG\Editor;

use ACP3\Core\ACL;
use ACP3\Core\Assets\IncludeJs;
use ACP3\Core\WYSIWYG\Editor\Textarea;
use ACP3\Modules\ACP3\Filemanager\Helpers;

/**
 * Implementation of the AbstractWYSIWYG class for CKEditor.
 */
class CKEditor extends Textarea
{
    private bool $isInitialized = false;

    public function __construct(private readonly ACL $acl, private readonly IncludeJs $includeJs, private readonly ?Helpers $filemanagerHelpers = null)
    {
    }

    public function getFriendlyName(): string
    {
        return 'CKEditor';
    }

    public function setParameters(array $params = []): void
    {
        parent::setParameters($params);

        $this->config['toolbar'] = (isset($params['toolbar']) && $params['toolbar'] === 'simple') ? 'Basic' : 'Full';
    }

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
            $wysiwyg['advanced_replace_content'] = <<<JS
const editor = window.CKEditorInstances['{$wysiwyg['id']}'];

const insertPosition = editor.model.document.selection.getFirstPosition();
const viewFragment = editor.data.processor.toView(text);
const modelFragment = editor.data.toModel(viewFragment);

editor.model.insertContent( modelFragment, insertPosition);
JS;
        }

        return ['wysiwyg' => $wysiwyg];
    }

    /**
     * Configures the CKEditor instance.
     *
     * @throws \JsonException
     */
    private function configure(): string
    {
        // Full toolbar
        if (!isset($this->config['toolbar']) || $this->config['toolbar'] !== 'Basic') {
            $this->addFileManager();
        }

        return json_encode($this->config, JSON_THROW_ON_ERROR);
    }

    private function init(): string
    {
        if ($this->isInitialized === true) {
            return '';
        }

        $this->isInitialized = true;

        return $this->includeJs->add('Wysiwygckeditor', 'partials/ckeditor');
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
}
