{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="system|seo"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {$publication_period}
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">{lang t="gallery|title"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
                    </div>
                </div>
            </div>
            <div id="tab-2" class="tab-pane fade">
                {$SEO_FORM_FIELDS}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/gallery"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}