<div id="error-box" class="alert alert-warning alert-block">
    <h4>{lang t="system|failure"}</h4>
    <ul>
        {foreach $error_box.errors as $key => $value}
            <li{if intval($key) === 0} data-error="{$key}"{/if}>{$value}</li>
        {/foreach}
    </ul>
    {if $error_box.non_integer_keys === true}
        {javascripts}
            {include_js module="system" file="error_box"}
        {/javascripts}
    {/if}
</div>