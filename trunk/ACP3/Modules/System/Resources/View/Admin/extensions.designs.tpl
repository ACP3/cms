{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    {redirect_message}
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>{lang t="system|name"}</th>
            <th>{lang t="system|description"}</th>
            <th>{lang t="system|author"}</th>
            <th>{lang t="system|version"}</th>
            <th>{lang t="system|options"}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $designs as $row}
            <tr>
                <td>{$row.name}</td>
                <td>{$row.description}</td>
                <td>{$row.author}</td>
                <td>{$row.version}</td>
                <td>
                    {if $row.selected == 1}
                        {icon path="16/apply"}
                    {else}
                        <a href="{uri args="acp/system/extensions/designs/dir_`$row.dir`"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">{icon path="16/cancel"}</a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/block}

{block JAVASCRIPTS append}
    {include_js module="system" file="forms"}
{/block}