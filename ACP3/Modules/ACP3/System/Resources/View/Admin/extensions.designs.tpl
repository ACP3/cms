{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>{lang t="system|name"}</th>
            <th>{lang t="system|description"}</th>
            <th>{lang t="system|author"}</th>
            <th class="text-right">{lang t="system|version"}</th>
            <th class="text-center">{lang t="system|options"}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $designs as $row}
            <tr>
                <td>{$row.name}</td>
                <td>{$row.description}</td>
                <td>{$row.author}</td>
                <td class="text-right">{$row.version}</td>
                <td class="text-center">
                    {if $row.selected === true}
                        {icon iconSet="solid" icon="toggle-on" cssSelectors="text-success"}
                    {else}
                        <a href="{uri args="acp/system/extensions/designs/dir_`$row.internal_name`"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                            {icon iconSet="solid" icon="toggle-off" cssSelectors="text-danger"}
                        </a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {js_libraries enable="ajax-form"}
{/block}
