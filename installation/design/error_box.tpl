<div id="error-box" class="alert alert-warning alert-block">
    <h4>{lang t="install|failure"}</h4>
    <ul>
        {foreach $error_box.errors as $key => $value}
            <li{if intval($key) === 0} data-error="{$key}"{/if}>{$value}</li>
        {/foreach}
    </ul>
    {if $error_box.non_integer_keys === true}
        <script type="text/javascript" src="{$ROOT_DIR}ACP3/Modules/System/Resources/Assets/js/error_box.js"></script>
    {/if}
</div>