{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="seo|seo"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label required">{lang t="gallery|title"}</label>

                        <div class="col-sm-10">
                            <input class="form-control"
                                   type="text"
                                   name="title"
                                   id="title"
                                   value="{$form.title}"
                                   maxlength="120"
                                   data-seo-slug-base="true"
                                   required>
                        </div>
                    </div>
                </div>
                <div id="tab-2" class="tab-pane fade">
                    {include file="asset:Seo/Partials/seo_fields.tpl" seo=$SEO_FORM_FIELDS}
                </div>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/gallery"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
