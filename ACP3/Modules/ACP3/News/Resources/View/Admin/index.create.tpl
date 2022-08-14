{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="news-admin-edit-form"}
        {tab title={lang t="system|publication"}}
            {include file="asset:System/Partials/form.publication.tpl" options=$active publication_period=[$form.start, $form.end]}
        {/tab}
        {tab title={lang t="news|news"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="news|title"}}
            {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true label={lang t="news|text"}}
            <div class="row mb-3">
                <label for="cat" class="col-md-2 col-form-label required">{lang t="categories|category"}</label>

                <div class="col-md-10">
                    {include file="asset:Categories/Partials/create_list.tpl" categories=$categories}
                </div>
            </div>
            {if !empty($options)}
                {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
            {/if}
            {event name="news.layout.upsert" form_data=$form}
        {/tab}
        {tab title={lang t="news|hyperlink"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="link_title" value=$form.link_title maxlength=120 label={lang t="news|link_title"}}
            {include file="asset:System/Partials/form_group.input_url.tpl" name="uri" value=$form.uri maxlength=120 label={lang t="news|uri"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$target label={lang t="news|target_page"}}
        {/tab}
        {event name="core.layout.form_extension" uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/news"}}
{/block}
