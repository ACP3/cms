{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal " data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="news|news"}</a></li>
            <li><a href="#tab-3" data-toggle="tab">{lang t="news|hyperlink"}</a></li>
            <li><a href="#tab-4" data-toggle="tab">{lang t="system|seo"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {$publication_period}
            </div>
            <div id="tab-2" class="tab-pane fade">
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">{lang t="news|title"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120">
                    </div>
                </div>
                <div class="form-group">
                    <label for="text" class="col-lg-2 control-label">{lang t="news|text"}</label>

                    <div class="col-lg-10">{wysiwyg name="text" value="`$form.text`" height="250"}</div>
                </div>
                <div class="form-group">
                    <label for="cat" class="col-lg-2 control-label">{lang t="categories|category"}</label>

                    <div class="col-lg-10">
                        {$categories}
                    </div>
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
                <div class="form-group">
                    <label for="link-title" class="col-lg-2 control-label">{lang t="news|link_title"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="link_title" id="link-title" value="{$form.link_title}" maxlength="120">
                    </div>
                </div>
                <div class="form-group">
                    <label for="uri" class="col-lg-2 control-label">{lang t="news|uri"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="url" name="uri" id="uri" value="{$form.uri}" maxlength="120">
                    </div>
                </div>
                <div class="form-group">
                    <label for="target" class="col-lg-2 control-label">{lang t="news|target_page"}</label>

                    <div class="col-lg-10">
                        <select class="form-control" name="target" id="target">
                            {foreach $target as $row}
                                <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="tab-4" class="tab-pane fade">
                {$SEO_FORM_FIELDS}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/news"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}