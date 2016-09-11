{extends file="asset:System/layout.ajax-form.tpl"}

{block AJAX_FORM_CONTENT}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 label={lang t="menus|menu_bar_title"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="index_name" value=$form.index_name required=true maxlength=20 label={lang t="menus|index_name"}}
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/menus"}}
{/block}
