{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="gallery-admin-edit-form"}
        {tab title={lang t="system|publication"}}
            {include file="asset:System/Partials/form.publication.tpl" options=$active publication_period=[$form.start, $form.end]}
        {/tab}
        {tab title={lang t="system|general_statements"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 data_attributes=['seo-slug-base' => 'true'] label={lang t="gallery|title"}}
            {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="description" value=$form.description toolbar="simple" label={lang t="system|description"}}
            {event name="gallery.layout.upsert" form_data=$form}
        {/tab}
        {event name="core.layout.form_extension"  uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/gallery"}}
{/block}
