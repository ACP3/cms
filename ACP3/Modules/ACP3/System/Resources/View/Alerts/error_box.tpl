{extends file="asset:system/alerts/error_box_layout.tpl"}

{block CONTENT}
    <div id="error-box" class="alert alert-warning alert-block">
        <h4>{lang t="system|failure"}</h4>
        <ul>
            {foreach $error_box.errors as $key => $value}
                <li{if intval($key) === 0} data-error="{$key}"{/if}>{$value}</li>
            {/foreach}
        </ul>
    </div>
{/block}
