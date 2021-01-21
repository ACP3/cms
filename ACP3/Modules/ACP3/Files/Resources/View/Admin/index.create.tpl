{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="file-admin-edit-form"}
        {tab title={lang t="system|publication"}}
            {include file="asset:System/Partials/form.publication.tpl" options=$active publication_period=[$form.start, $form.end]}
        {/tab}
        {tab title={lang t="files|file_statements"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="files|title"}}
            {block FILES_FILE_UPLOAD}
                <div id="file-internal-toggle">
                    {include file="asset:System/Partials/form_group.input_file.tpl" name="file_internal" labelRequired=true label={lang t="files|file"}}
                </div>
            {/block}
                <div id="file-external-toggle">
                    {include file="asset:System/Partials/form_group.input_url.tpl" name="file_external" value=$form.file_external labelRequired=true maxlength=120 label={lang t="files|uri"}}
                    <div class="form-group">
                        <label for="filesize" class="col-sm-2 control-label required">{lang t="files|filesize"}</label>

                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-8">
                                    <input class="form-control" type="number" name="filesize" id="filesize" value="{$form.filesize}" maxlength="15" min="0">
                                </div>
                                <div class="col-sm-4">
                                    <label for="unit" class="sr-only">{lang t="files|unit"}</label>
                                    <select class="form-control" name="unit" id="unit">
                                        {foreach $units as $row}
                                            <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {include file="asset:System/Partials/form_group.checkbox.tpl" options=$external}
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true toolbar="simple" label={lang t="system|description"}}
                <div class="form-group">
                    <label for="cat" class="col-sm-2 control-label required">{lang t="categories|category"}</label>

                    <div class="col-sm-10">
                        {include file="asset:Categories/Partials/create_list.tpl" categories=$categories}
                    </div>
                </div>
            {event name="files.layout.upsert" form_data=$form}
        {/tab}
    {event name="core.layout.form_extension"  uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/files"}}
    {javascripts}
        {include_js module="files" file="admin/index.create"}
    {/javascripts}
{/block}
