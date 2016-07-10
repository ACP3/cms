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
        {include file="asset:System/Partials/mark.tpl"}
    {/if}
    {include file="asset:System/Partials/datatable.tpl" dt=$dataTable.config}
{else}
    {include file="asset:System/Partials/no_results.tpl"}
{/if}
