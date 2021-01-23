{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="article-admin-edit-form"}
        {tab title={lang t="system|publication"}}
            {include file="asset:System/Partials/form.publication.tpl" options=$active publication_period=[$form.start, $form.end]}
        {/tab}
        {tab title={lang t="articles|page_statements"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=255 data_attributes=['seo-slug-base' => 'true'] label={lang t="articles|title"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="subtitle" value=$form.subtitle maxlength=255 label={lang t="articles|subtitle"}}
            {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="text" value=$form.text required=true advanced=true label={lang t="articles|text"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$layouts labelRequired=true label={lang t="articles|layout"}}
        {/tab}
        {event name="core.layout.form_extension" uri_pattern=$SEO_URI_PATTERN path=$SEO_ROUTE_NAME}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/articles"}}
{/block}
