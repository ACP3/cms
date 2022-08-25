{extends file="asset:$LAYOUT"}

{block CONTENT}
    <div id="error-box" class="alert alert-warning" role="alert">
        <h4 class="alert-heading">{lang t="system|failure"}</h4>
        <ul class="mb-0">
            {foreach $error_box.errors as $key => $value}
                <li{if intval($key) === 0} data-error="{$key}"{/if}>{$value}</li>
            {/foreach}
        </ul>
    </div>
{/block}
