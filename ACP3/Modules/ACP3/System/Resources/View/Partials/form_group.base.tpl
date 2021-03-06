<div{if isset($formGroupId)} id="{$formGroupId}"{/if} class="form-group{if !empty($formGroupSelector)}{$formGroupSelector}{/if}">
    {if !empty($label)}
        <label class="col-sm-2 control-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}"
               {block FORM_GROUP_LABEL_ID}for="{if !isset($options[0].id)}{$name|replace:'_':'-'}{else}{$options[0].id}{/if}"{/block}>
            {$label}
        </label>
    {/if}

    {if isset($cssSelector) && empty($cssSelector)}
        {block FORM_GROUP_FORM_FIELD}{/block}
        {if !empty($help)}
            <p class="help-block">{$help}</p>
        {/if}
    {else}
        <div class="{if isset($cssSelector)}{$cssSelector}{else}{if empty($label)}col-sm-offset-2 {/if}{if !empty($columnSelector)}{$columnSelector}{else}col-sm-10{/if}{/if}">
            {block FORM_GROUP_FORM_FIELD}{/block}
            {if !empty($help)}
                <p class="help-block">{$help}</p>
            {/if}
        </div>
    {/if}
</div>
