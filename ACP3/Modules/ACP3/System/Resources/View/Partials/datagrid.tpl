{if !empty($dataTable.results)}
    <table id="{$dataTable.identifier}" class="table table-striped table-hover datagrid">
        <thead>
        <tr>
            {$dataTable.header}
        </tr>
        </thead>
        {if !$dataTable.config.ajax}
            <tbody>
            {$dataTable.results}
            </tbody>
        {/if}
    </table>
    {if $dataTable.can_delete === true}
        {include file="asset:System/Partials/mark.tpl"}
    {/if}
    {include file="asset:System/Partials/datatable.tpl" dt=$dataTable.config}
{else}
    {include file="asset:System/Partials/no_results.tpl"}
{/if}
