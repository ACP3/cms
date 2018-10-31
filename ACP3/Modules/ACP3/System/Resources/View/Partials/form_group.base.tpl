<div class="form-group{if isset($formGroupSelector)} {$formGroupSelector}{else} row{/if}">
    {if !empty($label)}
        <label class="col-sm-2 col-form-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}{if isset($labelSelector)} {$labelSelector}{/if}"
               {block FORM_GROUP_LABEL_ID}for="{$options.0.id}"{/block}>
            {$label}
        </label>
    {/if}

    {if isset($cssSelector) && empty($cssSelector)}
        {block FORM_GROUP_FORM_FIELD}{/block}
        {if !empty($help)}
            <small class="form-text text-muted">{$help}</small>
        {/if}
    {else}
        <div class="{if isset($cssSelector)}{$cssSelector}{else}{if empty($label)}offset-sm-2 {/if}{if !empty($columnSelector)}{$columnSelector}{else}col-sm-10{/if}{/if}">
            {block FORM_GROUP_FORM_FIELD}{/block}
            {if !empty($help)}
                <small class="form-text text-muted">{$help}</small>
            {/if}
        </div>
    {/if}
</div>
