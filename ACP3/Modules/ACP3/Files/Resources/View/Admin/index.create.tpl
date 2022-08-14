{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="file-admin-edit-form"}
        {tab title={lang t="system|publication"}}
            {include file="asset:System/Partials/form.publication.tpl" options=$active publication_period=[$form.start, $form.end]}
        {/tab}
        {tab title={lang t="files|file_statements"}}
            {block FILES_FILE_UPLOAD}
                {include file="asset:System/Partials/form_group.checkbox.tpl" options=$external}
                {include file="asset:System/Partials/form_group.input_file.tpl" name="file_internal" formGroupId="file-internal-toggle" labelRequired=true label={lang t="files|file"}}
            {/block}
            <div id="file-external-toggle">
                {include file="asset:System/Partials/form_group.input_url.tpl" name="file_external" value=$form.file_external labelRequired=true maxlength=120 label={lang t="files|uri"}}
                {include file="asset:Files/Partials/form_group.filesize.tpl" name="filesize" value=$form.filesize units=$units labelRequired=true maxlength=120 label={lang t="files|filesize"}}
            </div>
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="files|title"}}
            {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true toolbar="simple" label={lang t="system|description"}}
            {include file="asset:Categories/Partials/form_group.categories.tpl" name="cat" categories=$categories required=true label={lang t="categories|category"}}
            {event name="files.layout.upsert" form_data=$form}
        {/tab}
    {event name="core.layout.form_extension"  uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/files"}}
    {javascripts}
        {include_js module="files" file="admin/index.create"}
    {/javascripts}
{/block}
