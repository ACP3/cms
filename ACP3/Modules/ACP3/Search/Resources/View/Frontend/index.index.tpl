{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_BEFORE_AJAX_FORM}
    {redirect_message}
{/block}
{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="search_term" value=$form.search_term required=true label={lang t="search|search_term"}}
    {include file="asset:System/Partials/form_group.submit.tpl" name="add_answer" button_type="button" submit_btn_class="btn-outline-secondary" attributes=['id' => "search-advanced-toggle"] submit_label={lang t="search|advanced_search"}}
    <div id="search-advanced-wrapper" class="d-none">
        {include file="asset:System/Partials/form_group.button_group_checkbox.tpl" options=$search_mods required=true label={lang t="search|search_after_modules"}}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$search_areas required=true label={lang t="search|search_after_areas"}}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$sort_hits required=true label={lang t="search|sort_hits"}}
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" submit_label={lang t="search|submit_search"}}
    {javascripts}
        {include_js module="search" file="frontend/index.index"}
    {/javascripts}
{/block}
