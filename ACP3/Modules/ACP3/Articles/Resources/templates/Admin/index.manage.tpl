{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="articles|page_statements"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$active name="active" required=true label={lang t="articles|active"}}
                <div id="publication-period-wrapper">
                    {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
                </div>
            </div>
            <div id="tab-2" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="articles|title"}}
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true advanced=true label={lang t="articles|text"}}
                {event name="menus.layout.render_manage_menu_item" uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
            </div>
            {event name="seo.layout.render_form_fields" uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/articles"}}
    {javascripts}
        {include_js module="articles" file="admin/index.manage"}
    {/javascripts}
{/block}
