{if !empty($dataTable.results)}
    <table id="{$dataTable.identifier}" class="table table-striped table-hover datagrid">
        <thead>
        <tr>
            {$dataTable.header}
        </tr>
        </thead>
        <tbody>
        {$dataTable.results}
        </tbody>
    </table>
    {if $dataTable.can_delete === true}
        {include file="asset:system/mark.tpl"}
    {/if}
    {include file="asset:system/datatable.tpl" dt=$dataTable.config}
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="system|no_entries"}</strong>
    </div>
{/if}
