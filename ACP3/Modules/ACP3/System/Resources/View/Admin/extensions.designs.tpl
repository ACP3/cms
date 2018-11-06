{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <table class="table table-striped table-hover datagrid">
        <thead>
        <tr>
            <th>{lang t="system|name"}</th>
            <th class="datagrid-column__max-width">{lang t="system|description"}</th>
            <th>{lang t="system|author"}</th>
            <th class="text-right">{lang t="system|version"}</th>
            <th>{lang t="system|options"}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $designs as $row}
            <tr>
                <td>{$row.name}</td>
                <td>{$row.description}</td>
                <td>{$row.author}</td>
                <td class="text-right">{$row.version}</td>
                <td>
                    {if $row.selected == 1}
                        <i class="fas fa-check text-success"></i>
                    {else}
                        <a href="{uri args="acp/system/extensions/designs/dir_`$row.dir`"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                            <i class="fas fa-trash text-danger"></i>
                        </a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/block}
