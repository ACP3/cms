{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {block PERMISSIONS_RESOURCES_MODULE}
        {include file="asset:System/Partials/form_group.select.tpl" options=$modules required=true label={lang t="system|module"}}
    {/block}
    {include file="asset:System/Partials/form_group.select.tpl" options=$areas required=true label={lang t="permissions|area"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="controller" value=$form.controller required=true maxlength=255 label={lang t="permissions|controller"}}
    {include file="asset:System/Partials/form_group.input_text.tpl" name="resource" value=$form.resource required=true maxlength=255 label={lang t="permissions|resource_name"}}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/permissions/resources"}}
{/block}
