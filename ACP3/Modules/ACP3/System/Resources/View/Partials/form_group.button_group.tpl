<div class="form-group">
    <label for="{$options.0.id}"
           class="col-sm-2 control-label{if (isset($required) && $required === true)} required{/if}">
        {$label}
    </label>
    <div class="col-sm-10">
        <div class="btn-group" data-toggle="buttons">
            {foreach $options as $row}
                <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                    <input type="radio"
                           name="{$row.name}"
                           id="{$row.id}"
                           value="{$row.value}"
                            {if isset($required) && $required === true} required{/if}
                            {$row.checked}>
                    {$row.lang}
                </label>
            {/foreach}
        </div>
        {if !empty($help)}
            <p class="help-block">{$help}</p>
        {/if}
    </div>
</div>
