{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal " data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="files|file_statements"}</a></li>
            <li><a href="#tab-3" data-toggle="tab">{lang t="system|seo"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {$publication_period}
            </div>
            <div id="tab-2" class="tab-pane fade">
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">{lang t="files|title"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120">
                    </div>
                </div>
                <div class="form-group">
                    <label for="file-internal" class="col-lg-2 control-label">{lang t="files|filename"}</label>

                    <div class="col-lg-10">
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
                <div id="external-filesize" class="form-group">
                    <label for="filesize" class="col-lg-2 control-label">{lang t="files|filesize"}</label>

                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-lg-10">
                                <input class="form-control" type="text" name="filesize" id="filesize" value="{$form.filesize}" maxlength="15">
                            </div>
                            <div class="col-lg-2">
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
                    <label for="text" class="col-lg-2 control-label">{lang t="system|description"}</label>

                    <div class="col-lg-10">{wysiwyg name="text" value="`$form.text`" height="200" toolbar="simple"}</div>
                </div>
                <div class="form-group">
                    <label for="cat" class="col-lg-2 control-label">{lang t="categories|category"}</label>

                    <div class="col-lg-10">{$categories}</div>
                </div>
                {if isset($options)}
                    <div class="form-group">
                        <label for="{$options.0.name}" class="col-lg-2 control-label">{lang t="system|options"}</label>

                        <div class="col-lg-10">
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
                {$SEO_FORM_FIELDS}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/files"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="files" file="acp"}
{include_js module="system" file="forms"}