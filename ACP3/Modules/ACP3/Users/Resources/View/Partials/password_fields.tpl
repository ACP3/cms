{if empty($field_name)}
    {$field_name='pwd'}
{/if}
{if empty($translator_phrase)}
    {$translator_phrase='pwd'}
{/if}
<div class="form-group">
    <label for="{$field_name|replace:'_':'-'}"
           class="col-sm-2 control-label{if isset($required) && $required === true} required{/if}">
        {lang t="users|`$translator_phrase`"}
    </label>
    <div class="col-sm-10">
        <input class="form-control"
               type="password"
               name="{$field_name}"
               id="{$field_name|replace:'_':'-'}"
               value=""
                {if isset($required) && $required === true} required{/if}>
    </div>
</div>
<div class="form-group">
    <label for="{$field_name|replace:'_':'-'}-repeat"
           class="col-sm-2 control-label{if isset($required) && $required === true} required{/if}">
        {lang t="users|`$translator_phrase`_repeat"}
    </label>
    <div class="col-sm-10">
        <input class="form-control"
               type="password"
               name="{$field_name}_repeat"
               id="{$field_name|replace:'_':'-'}-repeat"
               value=""
                {if isset($required) && $required === true} required{/if}>
    </div>
</div>
