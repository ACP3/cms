{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a href="#tab-1" class="nav-link active" data-toggle="tab">{lang t="system|publication"}</a></li>
        <li class="nav-item"><a href="#tab-2" class="nav-link" data-toggle="tab">{lang t="articles|page_statements"}</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane fade show active">
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$active name="active" required=true label={lang t="articles|active"}}
            <div id="publication-period-wrapper">
                {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
            </div>
        </div>
        <div id="tab-2" class="tab-pane fade">
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=255 data_attributes=['seo-slug-base' => 'true'] label={lang t="articles|title"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="subtitle" value=$form.subtitle maxlength=255 label={lang t="articles|subtitle"}}
            {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true advanced=true label={lang t="articles|text"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$layouts labelRequired=true label={lang t="articles|layout"}}
            {event name="menus.layout.render_manage_menu_item" uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
        </div>
        {event name="core.layout.form_extension" uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/articles"}}
    {javascripts}
        {include_js module="articles" file="admin/index.create"}
    {/javascripts}
{/block}
