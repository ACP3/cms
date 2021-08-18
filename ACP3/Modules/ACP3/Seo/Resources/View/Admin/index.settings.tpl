{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="seo-admin-settings-form"}
        {tab title={lang t="system|general"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="meta_description" value=$form.meta_description maxlength=120 label={lang t="seo|description"}}
            {include file="asset:System/Partials/form_group.textarea.tpl" name="meta_keywords" value=$form.meta_keywords label={lang t="seo|keywords"} help={lang t="seo|keywords_separate_with_commas"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$robots required=true label={lang t="seo|robots"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$index_paginated_content required=true label={lang t="seo|index_paginated_content"}}
        {/tab}
        {tab title={lang t="seo|sitemap"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$sitemap_is_enabled required=true label={lang t="seo|sitemap_enable"}}
            <div id="seo-sitemap-wrapper">
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$sitemap_save_mode required=true label={lang t="seo|sitemap_save_mode"}}
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$sitemap_separate required=true label={lang t="seo|sitemap_separate"}}
                {if {has_permission path="admin/seo/index/sitemap"}}
                    <div class="row mb-3">
                        <div class="offset-sm-2 col-sm-10">
                            <a href="{uri args="acp/seo/index/sitemap"}"
                               class="btn btn-outline-secondary"
                               data-ajax-form="true">
                                {lang t="seo|sitemap_refresh_now"}
                            </a>
                        </div>
                    </div>
                {/if}
            </div>
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/seo"}}
    {javascripts}
        {include_js module="seo" file="admin/index.settings"}
    {/javascripts}
{/block}
