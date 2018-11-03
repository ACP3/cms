{extends file="asset:System/layout.error_box.tpl"}

{block CONTENT}
    <div id="error-box" class="alert alert-warning">
        <h5 class="alert-heading">{lang t="system|failure"}</h5>
        <ul class="mb-0">
            {foreach $error_box.errors as $key => $value}
                <li{if intval($key) === 0} data-error="{$key}"{/if}>{$value}</li>
            {/foreach}
        </ul>
    </div>
{/block}
