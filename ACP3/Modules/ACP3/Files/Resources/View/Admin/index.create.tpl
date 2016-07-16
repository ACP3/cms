{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="files|file_statements"}</a></li>
                <li><a href="#tab-3" data-toggle="tab">{lang t="seo|seo"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
                </div>
                <div id="tab-2" class="tab-pane fade">
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label required">{lang t="files|title"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
                        </div>
                    </div>
                    {block FILES_FILE_UPLOAD}
                        <div class="form-group">
                            <label for="file-internal" class="col-sm-2 control-label required">{lang t="files|file"}</label>

                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <label for="external">
                                        <input type="checkbox" name="external" id="external" value="1"{$checked_external}>
                                        {lang t="files|external_resource"}
                                    </label>
                                </div>
                                <input type="file" name="file_internal" id="file-internal">
                                <input class="form-control" type="url" name="file_external" id="file-external" value="{$form.file_external}" maxlength="120">
                            </div>
                        </div>
                    {/block}
                    <div id="external-filesize" class="form-group">
                        <label for="filesize" class="col-sm-2 control-label required">{lang t="files|filesize"}</label>

                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="filesize" id="filesize" value="{$form.filesize}" maxlength="15">
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control" name="unit" id="unit">
                                        {foreach $units as $row}
                                            <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text" class="col-sm-2 control-label required">{lang t="system|description"}</label>

                        <div class="col-sm-10">{wysiwyg name="text" value="`$form.text`" height="200" toolbar="simple"}</div>
                    </div>
                    <div class="form-group">
                        <label for="cat" class="col-sm-2 control-label required">{lang t="categories|category"}</label>

                        <div class="col-sm-10">
                            {include file="asset:Categories/Partials/create_list.tpl" categories=$categories}
                        </div>
                    </div>
                    {if isset($options)}
                        <div class="form-group">
                            <label for="{$options.0.name}" class="col-sm-2 control-label">{lang t="system|options"}</label>

                            <div class="col-sm-10">
                                {foreach $options as $row}
                                    <div class="checkbox">
                                        <label for="{$row.name}">
                                            <input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
                                            {$row.lang}
                                        </label>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    {/if}
                </div>
                <div id="tab-3" class="tab-pane fade">
                    {include file="asset:seo/seo_fields.tpl" seo=$SEO_FORM_FIELDS}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/files"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="files" file="admin/acp"}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
