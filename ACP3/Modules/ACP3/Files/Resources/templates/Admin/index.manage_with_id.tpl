{extends file="asset:Files/Admin/index.manage.tpl"}

{block FILES_FILE_UPLOAD}
    {include file="asset:System/Partials/form_group.static.tpl" value=$current_file label={lang t="files|current_file"}}
    <div id="file-internal-toggle">
        {include file="asset:System/Partials/form_group.input_file.tpl" name="file_internal" labelRequired=true label={lang t="files|new_file"}}
    </div>
{/block}
