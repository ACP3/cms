{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="system|general_statements"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$active name="active" required=true label={lang t="files|active"}}
                <div id="publication-period-wrapper">
                    {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
                </div>
            </div>
            <div id="tab-2" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="gallery|title"}}
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="description" value=$form.description toolbar="simple" label={lang t="system|description"}}
            </div>
            {event name="core.layout.form_extension"  uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/gallery"}}
    {javascripts}
        {include_js module="gallery" file="admin/index.create"}
    {/javascripts}
{/block}
