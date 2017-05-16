{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <button type="submit" name="submit" class="btn{if !empty($submit_btn_class)} {$submit_btn_class}{else} btn-primary{/if}">
        {if !empty($submit_label)}
            {$submit_label}
        {else}
            {lang t="system|submit"}
        {/if}
    </button>
    {if !empty($back_url)}
        <a href="{$back_url}" class="btn{if !empty($back_btn_class)} {$back_btn_class}{else} btn-default{/if}">
            {if !empty($back_label)}
                {$back_label}
            {else}
                {lang t="system|cancel"}
            {/if}
        </a>
    {/if}
    {if !empty($form_token)}
        {$form_token}
    {/if}
{/block}
