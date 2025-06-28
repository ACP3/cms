{if $IS_AJAX}
    {$LAYOUT='System/layout.modal.tpl'}
    {$modal=['id' => 'js-edit-categories']}
{/if}

{$is_multipart=true}

{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength="120" label={lang t="categories|title"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="description" value=$form.description maxlength="120" label={lang t="system|description"}}
    {include file="asset:System/Partials/form_group.input_file.tpl" name="picture" label={lang t="categories|picture"}}
    {block CATEGORIES_MODULE}
        {include file="asset:System/Partials/form_group.select.tpl" options=$mod_list required=true label={lang t="categories|module"}}
    {/block}
    {if !empty($category_tree)}
        {include file="asset:System/Partials/form_group.select.tpl" options=$category_tree label={lang t="categories|superior_category"} emptyOptionLabel={lang t="categories|no_superior_category"}}
    {else}
        <input type="hidden" name="parent_id" value="{$form.parent_id}">
    {/if}
    {if $IS_AJAX}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url=$BACK_URI}
    {else}
        {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url=$BACK_URI}
    {/if}
{/block}
