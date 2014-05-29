{if isset($redirect_message)}
    {$redirect_message}
{/if}
<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th>{lang t="system|language"}</th>
        <th>{lang t="system|options"}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $languages as $row}
        <tr>
            <td>{$row.name}</td>
            <td>
                {if $row.selected === true}
                    {icon path="16/apply"}
                {else}
                    <a href="{uri args="acp/system/extensions/languages/language_`$row.iso`"}">{icon path="16/cancel"}</a>
                {/if}
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>