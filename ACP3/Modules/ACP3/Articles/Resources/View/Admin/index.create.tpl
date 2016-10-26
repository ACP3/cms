{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="articles|page_statements"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {datepicker name=['start', 'end'] value=[$form.start, $form.end]}
            </div>
            <div id="tab-2" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="articles|title"}}
                {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true advanced=true label={lang t="articles|text"}}
                {if !empty($options)}
                    {include file="asset:System/Partials/form_group.checkbox.tpl" label={lang t="system|options"}}
                    {include file="asset:Menus/Partials/create_menu_item.tpl"}
                {/if}
            </div>
            {include file="asset:Seo/Partials/tab_seo_fields.tpl" seo=$SEO_FORM_FIELDS}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/articles"}}
    {javascripts}
        {include_js module="articles" file="admin/index.create"}
    {/javascripts}
{/block}
