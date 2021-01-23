{extends file="asset:Files/Admin/index.create.tpl"}

{block FILES_FILE_UPLOAD}
    {include file="asset:System/Partials/form_group.static.tpl" value=$current_file label={lang t="files|current_file"}}
    {include file="asset:System/Partials/form_group.checkbox.tpl" options=$external}
    <div id="file-internal-toggle">
        {include file="asset:System/Partials/form_group.input_file.tpl" name="file_internal" labelRequired=true label={lang t="files|new_file"}}
    </div>
{/block}
