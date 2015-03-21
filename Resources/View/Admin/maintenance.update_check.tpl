{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($update)}
        <div class="alert alert-block alert-{$update.class}">
            <h4>{$update.text}</h4>
            <ul>
                <li><strong>{lang t="system|installed_version"}:</strong> {$update.installed_version}</li>
                <li><strong>{lang t="system|current_version"}:</strong> {$update.current_version}</li>
            </ul>
        </div>
    {else}
        <div class="alert">
            <strong class="text-center">{lang t="system|error_update_check"}</strong>
        </div>
    {/if}
{/block}